<?php

declare(strict_types=1);

namespace DTOBundle\Trait;

use DTOBundle\Mapper\AutoMapperAwareInterface;

trait MapperTrait
{
    /**
     * @var AutoMapperAwareInterface
     */
    protected $mapper;

    /**
     * @return AutoMapperAwareInterface
     */
    public function getMapper(): AutoMapperAwareInterface
    {
        return $this->mapper;
    }

    /**
     * @required
     * @param AutoMapperAwareInterface $mapper
     *
     * @return self
     */
    public function setMapper(AutoMapperAwareInterface $mapper): self
    {
        $this->mapper = $mapper;

        return $this;
    }
}
