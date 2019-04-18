<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PokemonCharacteristicRepository")
 */
class PokemonCharacteristic
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"list", "detail"})
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pokemon", inversedBy="characteristics", cascade={"persist"})
     */
    private $pokemon;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Characteristic", cascade={"persist"})
     * @Serializer\Groups({"list", "detail"})
     */
    private $characteristic;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return PokemonCharacteristic
     */
    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPokemon() :Pokemon
    {
        return $this->pokemon;
    }

    /**
     * @param Pokemon $pokemon
     * @return PokemonCharacteristic
     */
    public function setPokemon(Pokemon $pokemon): self
    {
        $this->pokemon = $pokemon;

        return $this;
    }

    /**
     * @return Characteristic
     */
    public function getCharacteristic(): Characteristic
    {
        return $this->characteristic;
    }

    /**
     * @param mixed $characteristic
     * @return PokemonCharacteristic
     */
    public function setCharacteristic(Characteristic $characteristic): self
    {
        $this->characteristic = $characteristic;
        return $this;
    }
}
