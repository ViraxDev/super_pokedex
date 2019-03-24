<?php

namespace App\Serializer\Handler;

use App\Entity\Pokemon;
use App\Entity\PokemonCharacteristic;
use App\Entity\Type;
use App\Repository\CharacteristicRepository;
use App\Repository\TypeRepository;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PokemonHandler implements SubscribingHandlerInterface
{
    /**
     * @var TypeRepository $typeRepository
     */
    private $typeRepository;

    /**
     * @var CharacteristicRepository $characteristicRepository
     */
    private $characteristicRepository;

    /**
     * @var array $characteristicsAvailable
     */
    private $characteristicsAvailable = [];

    public function __construct(TypeRepository $typeRepository, CharacteristicRepository $characteristicRepository)
    {
        $this->typeRepository = $typeRepository;
        $this->characteristicRepository = $characteristicRepository;
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => 'App\Entity\Pokemon',
                'method' => 'serialize',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'format' => 'json',
                'type' => 'App\Entity\Pokemon',
                'method' => 'deserialize',
            ]
        ];
    }

    public function serialize(JsonSerializationVisitor $visitor, Pokemon $pokemon, array $type)
    {
        $characteritics = [];

        foreach ($pokemon->getCharacteristics() as $pokemonCharacteristic) {
            $label = $pokemonCharacteristic->getCharacteristic()->getLabel();
            $characteritics[$label] = $pokemonCharacteristic->getValue();
        }

        $updatedDate = $pokemon->getUpdatedAt();

        $data = [
            'id' => $pokemon->getId(),
            'name' => $pokemon->getName(),
            'types' => $pokemon->getTypes(),
            'createdAt' => $pokemon->getCreatedAt()->format('d/m/Y H:i:s'),
            'updatedAt' => is_null($updatedDate) ? $updatedDate : $updatedDate->format('d/m/Y H:i:s'),
            'generation' => $pokemon->getGeneration(),
            'legendary' => $pokemon->isLegendary()
        ];

        $data = array_merge($data, $characteritics);

        return $visitor->visitArray($data, $type);
    }

    public function deserialize(JsonDeserializationVisitor $visitor, $data, array $type)
    {
        $pokemon = new Pokemon();

        $this->setCharacteristics($data, $pokemon);

        $this->checkFields($data);

        foreach ($data as $property => $value) {
            $method = 'set' . ucfirst($property);

            if ('types' === $property) {
                $this->setTypes($pokemon, $visitor->visitArray($value, $type));

                continue;
            }

            if (in_array($property, $this->characteristicsAvailable)) {
                continue;
            }

            try {
                call_user_func([$pokemon, $method], $value);
            } catch (\Exception $exception) {
                throw new BadRequestHttpException("Invalid field [$property] requested.");
            }
        }
        return $pokemon;
    }

    private function setCharacteristics(array $userData, Pokemon $pokemon)
    {
        $characteristicsData = $this->characteristicRepository->findAll();

        foreach ($characteristicsData as $characteristic) {
            $label = $characteristic->getLabel();
            $this->checkFields($userData, $label);

            if (!array_key_exists($label, $userData)) {
                throw new BadRequestHttpException("Field [$label] is missing.");
            }

            $pokemonCharac = new PokemonCharacteristic();
            $pokemonCharac->setValue($userData[$label]);

            $pokemonCharac->setCharacteristic($characteristic);
            $pokemon->addCharacteristic($pokemonCharac);

            $this->characteristicsAvailable[] = $label;
        }
    }

    /**
     * @param Pokemon $pokemon
     * @param $types
     */
    private function setTypes(Pokemon $pokemon, $types)
    {
        if (empty($types)) {
            throw new BadRequestHttpException("Invalid field [types] requested.");
        }

        foreach ($types as $type) {
            if (array_key_exists('label', $type)) {
                $foundType = $this->typeRepository->findOneByLabel($type['label']);

                if ($foundType) {
                    $pokemon->addType($foundType);
                    continue;
                }

                $freshType = new Type();
                $freshType->setLabel($type['label']);
                $pokemon->addType($freshType);
            } else {
                throw new BadRequestHttpException("Field [label] in [types] is missing.");
            }

            if (empty($pokemon->getTypes())) {
                throw new BadRequestHttpException("Invalid field [types] requested.");
            }
        }
    }

    /**
     * @param $data
     * @param string $field
     */
    private function checkFields($data, $field = '')
    {
        if ($field != '' && !array_key_exists($field, $data)) {
            throw new BadRequestHttpException("Field [$field] is missing.");
        }

        if ($field != '' && !array_key_exists('types', $data)) {
            throw new BadRequestHttpException("Field [types] is missing.");
        }
    }
}
