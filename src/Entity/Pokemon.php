<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeInterface;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @UniqueEntity(fields={"name"}, message="It looks like this Pokemon already exist !")
 * @ORM\Entity(repositoryClass="App\Repository\PokemonRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 */
class Pokemon
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"list"})
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255, unique=true)
     * @Serializer\Groups({"list", "detail"})
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Type", cascade={"persist"})
     * @Serializer\Groups({"list", "detail"})
     */
    private $types;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\Type("DateTime<'d/m/Y h:i:s'>")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"list", "detail"})
     * @Serializer\Type("DateTime<'d/m/Y h:i:s'>")
     */
    private $updatedAt;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"list", "detail"})
     */
    private $generation;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Groups({"list", "detail"})
     */
    private $legendary;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PokemonCharacteristic", mappedBy="pokemon", cascade={"persist", "remove"})
     * @Serializer\Groups({"list", "detail"})
     */
    private $characteristics;

    /**
     * Pokemon constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $this->types = new ArrayCollection();
        $this->createdAt = new DateTime('now');
        $this->characteristics = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Pokemon
     */
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

    /**
     * @param Type $type
     * @return Pokemon
     */
    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
        }

        return $this;
    }

    /**
     * @param Type $type
     * @return Pokemon
     */
    public function removeType(Type $type): self
    {
        if ($this->types->contains($type)) {
            $this->types->removeElement($type);
        }

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return Pokemon
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface|null $updatedAt
     * @return Pokemon
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGeneration(): ?int
    {
        return $this->generation;
    }

    /**
     * @param int $generation
     * @return Pokemon
     */
    public function setGeneration(int $generation): self
    {
        $this->generation = $generation;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isLegendary(): ?bool
    {
        return $this->legendary;
    }

    /**
     * @param bool $legendary
     * @return Pokemon
     */
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

    /**
     * @param PokemonCharacteristic $pokemonCharacteristic
     * @return Pokemon
     */
    public function addCharacteristic(PokemonCharacteristic $pokemonCharacteristic): self
    {
        if (!$this->characteristics->contains($pokemonCharacteristic)) {
            $this->characteristics[] = $pokemonCharacteristic;
            $pokemonCharacteristic->setPokemon($this);
        }

        return $this;
    }

    /**
     * @param PokemonCharacteristic $pokemonCharacteristic
     * @return Pokemon
     */
    public function removeCharacteristic(PokemonCharacteristic $pokemonCharacteristic): self
    {
        if ($this->characteristics->contains($pokemonCharacteristic)) {
            $this->characteristics->removeElement($pokemonCharacteristic);
        }

        return $this;
    }

    /**
     * @ORM\PreUpdate
     * @throws Exception
     */
    public function updateDate()
    {
        $this->setUpdatedAt(new DateTime('now'));
    }
}
