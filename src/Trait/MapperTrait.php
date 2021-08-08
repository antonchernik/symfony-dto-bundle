<?php

declare(strict_types=1);

namespace DTOBundle\Trait;

use DTOBundle\Mapper\AutoMapperAwareInterface;

trait MapperTrait
{
    protected AutoMapperAwareInterface $mapper;

    public function getMapper(): AutoMapperAwareInterface
    {
        return $this->mapper;
    }

    /**
     * @required
     */
    public function setMapper(AutoMapperAwareInterface $mapper): self
    {
        $this->mapper = $mapper;

        return $this;
    }
}
