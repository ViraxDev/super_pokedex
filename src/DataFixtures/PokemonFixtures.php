<?php

namespace App\DataFixtures;

use App\Entity\Characteristic;
use App\Entity\Pokemon;
use App\Entity\PokemonCharacteristic;
use App\Entity\Type;
use App\Repository\CharacteristicRepository;
use App\Repository\TypeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class PokemonFixtures
 * @package App\DataFixtures
 */
class PokemonFixtures extends Fixture
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    /**
     * @var TypeRepository $typeRepository
     */
    private $typeRepository;

    /**
     * @var CharacteristicRepository $characteristicRepository
     */
    private $characteristicRepository;

    public function __construct(ContainerInterface $container, TypeRepository $typeRepository, CharacteristicRepository $characteristicRepository)
    {
        $this->container = $container;
        $this->typeRepository = $typeRepository;
        $this->characteristicRepository = $characteristicRepository;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $upload_dir = $this->container->getParameter('app.upload_dir');

        if (($handle = fopen($upload_dir . "pokemon.csv", "r")) !== false) {
            $loop = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {

                //Avoid first line
                if (0 !== $loop) {
                    $this->createPokemon($manager, $data);
                }

                $loop++;
            }

            fclose($handle);
        }
    }

    private function createPokemon(ObjectManager $manager, array $data)
    {
        $data = $this->clean($data);

        $pokemon = new Pokemon();
        $pokemon
            ->setName($data[1])
            ->setGeneration($data[11])
            ->setLegendary($data[12]);

        $this->setTypes($pokemon, $data);

        $this->setCharacteristic($manager, $pokemon, $data);

        $manager->persist($pokemon);
        $manager->flush();
    }

    /**
     * Return an array with the correct data types
     *
     * @param array $data
     * @return array
     */
    private function clean(array $data): array
    {
        $tab = [];

        foreach ($data as $index => $value) {
            if ($value === "") {
                $tab[$index] = null;
                continue;
            }

            //boolean
            if ($index === 12) {
                $tab[$index] = $value === "False" ? false : true;
                continue;
            }

            //string
            if ($index === 1 || $index === 2 || $index === 3) {
                $tab[$index] = $value;
                continue;
            }

            //integer
            $tab[$index] = (int)$value;
        }

        return $tab;
    }

    private function setTypes(Pokemon $pokemon, array $data)
    {
        $type1 = $this->typeRepository->findOneByLabel($data[2]);

        if (is_null($type1)) {
            $type1 = new Type();
            $type1->setLabel($data[2]);
        }


        $pokemon->addType($type1);

        if (!is_null($data[3])) {
            $type2 = $this->typeRepository->findOneByLabel($data[3]);

            if (is_null($type2)) {
                $type2 = new Type();
                $type2->setLabel($data[3]);
            }

            $pokemon->addType($type2);
        }
    }

    /**
     * @param ObjectManager $manager
     * @param Pokemon $pokemon
     * @param array $data
     */
    private function setCharacteristic(ObjectManager $manager, Pokemon $pokemon, array $data): void
    {
        $total = $this->characteristicRepository->findOneByLabel('Total');
        $hp = $this->characteristicRepository->findOneByLabel('HP');
        $attack = $this->characteristicRepository->findOneByLabel('Attack');
        $defense = $this->characteristicRepository->findOneByLabel('Defense');
        $spe_attack = $this->characteristicRepository->findOneByLabel('Sp. Atk');
        $spe_defense = $this->characteristicRepository->findOneByLabel('Sp. Def');
        $speed = $this->characteristicRepository->findOneByLabel('Speed');

        if (is_null($total)) {
            $total = new Characteristic();
            $total->setLabel('Total');
        }

        if (is_null($hp)) {
            $hp = new Characteristic();
            $hp->setLabel('HP');
        }

        if (is_null($attack)) {
            $attack = new Characteristic();
            $attack->setLabel('Attack');
        }

        if (is_null($defense)) {
            $defense = new Characteristic();
            $defense->setLabel('Defense');
        }

        if (is_null($spe_attack)) {
            $spe_attack = new Characteristic();
            $spe_attack->setLabel('Sp. Atk');
        }

        if (is_null($spe_defense)) {
            $spe_defense = new Characteristic();
            $spe_defense->setLabel('Sp. Def');
        }

        if (is_null($speed)) {
            $speed = new Characteristic();
            $speed->setLabel('Speed');
        }

        $this
            ->createPokemonCharac($pokemon, $total, $data[4])
            ->createPokemonCharac($pokemon, $hp, $data[5])
            ->createPokemonCharac($pokemon, $attack, $data[6])
            ->createPokemonCharac($pokemon, $defense, $data[7])
            ->createPokemonCharac($pokemon, $spe_attack, $data[8])
            ->createPokemonCharac($pokemon, $spe_defense, $data[9])
            ->createPokemonCharac($pokemon, $speed, $data[10]);
    }

    /**
     * @param Pokemon $pokemon
     * @param Characteristic $charact
     * @param int $value
     * @return PokemonFixtures
     */
    private function createPokemonCharac(Pokemon $pokemon, Characteristic $charact, int $value): self
    {
        $pokemonCharacteristic = new PokemonCharacteristic();
        $pokemonCharacteristic
            ->setCharacteristic($charact)
            ->setValue($value);

        $pokemon->addCharacteristic($pokemonCharacteristic);

        return $this;
    }
}
