<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pokemon", inversedBy="characteristics", cascade={"persist"})
     */
    private $pokemon;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Characteristic", cascade={"persist"})
     */
    private $characteristic;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

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
