<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueEntity(fields={"name"}, message="It looks like this Pokemon already exist !")
 * @ORM\Entity(repositoryClass="App\Repository\PokemonRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Pokemon
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Type", cascade={"persist"})
     */
    private $types;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="integer")
     */
    private $generation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $legendary;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PokemonCharacteristic", mappedBy="pokemon", cascade={"persist", "remove"})
     */
    private $characteristics;

    /**
     * Pokemon constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->createdAt = new \DateTime('now');
        $this->characteristics = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Type[]
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        if ($this->types->contains($type)) {
            $this->types->removeElement($type);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getGeneration(): ?int
    {
        return $this->generation;
    }

    public function setGeneration(int $generation): self
    {
        $this->generation = $generation;

        return $this;
    }

    public function isLegendary(): ?bool
    {
        return $this->legendary;
    }

    public function setLegendary(bool $legendary): self
    {
        $this->legendary = $legendary;

        return $this;
    }

    /**
     * @return Collection|PokemonCharacteristic[]
     */
    public function getCharacteristics(): Collection
    {
        return $this->characteristics;
    }

    public function addCharacteristic(PokemonCharacteristic $pokemonCharacteristic): self
    {
        if (!$this->characteristics->contains($pokemonCharacteristic)) {
            $this->characteristics[] = $pokemonCharacteristic;
            $pokemonCharacteristic->setPokemon($this);
        }

        return $this;
    }

    public function removeCharacteristic(PokemonCharacteristic $pokemonCharacteristic): self
    {
        if ($this->characteristics->contains($pokemonCharacteristic)) {
            $this->characteristics->removeElement($pokemonCharacteristic);
        }

        return $this;
    }

    /**
     * @ORM\PreUpdate
     * @throws \Exception
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new \DateTime('now'));
    }
}
