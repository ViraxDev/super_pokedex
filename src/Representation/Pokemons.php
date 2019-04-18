<?php

declare(strict_types=1);

namespace App\Representation;

use JMS\Serializer\Annotation as Serializer;
use LogicException;
use Pagerfanta\Pagerfanta;

/**
 * Class Pokemons
 *
 *
 * @package App\Representation
 */
class Pokemons
{
    /**
     * @Serializer\Type("array<App\Entity\Pokemon>")
     */
    public $data;

    public $meta;

    public function __construct(Pagerfanta $data)
    {
        $this->data = $data->getCurrentPageResults();

        $this->addMeta('limit', $data->getMaxPerPage());
        $this->addMeta('current_items', count($data->getCurrentPageResults()));
        $this->addMeta('total_items', $data->getNbResults());
        $this->addMeta('offset', $data->getCurrentPageOffsetStart());
    }

    /**
     * @param string $name
     * @param int $value
     */
    public function addMeta(string $name, int $value): void
    {
        if (isset($this->meta[$name])) {
            throw new LogicException(sprintf('This meta already exists. You are trying to override this meta, use the setMeta method instead for the %s meta.', $name));
        }

        $this->setMeta($name, $value);
    }

    /**
     * @param string $name
     * @param int $value
     */
    public function setMeta(string $name, int $value): void
    {
        $this->meta[$name] = $value;
    }
}
