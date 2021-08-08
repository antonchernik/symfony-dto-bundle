<?php

declare(strict_types=1);

namespace DTOBundle\Trait;

use DTOBundle\Mapper\AutoMapperAwareInterface;

interface MapperAwareInterface
{
    public function getMapper(): AutoMapperAwareInterface;
    public function setMapper(AutoMapperAwareInterface $mapper): self;
}
