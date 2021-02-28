<?php

declare(strict_types=1);

namespace DTOBundle\Trait;

use Symfony\Component\Serializer\SerializerInterface;

interface SerializerAwareInterface
{
    public function getSerializer(): SerializerInterface;

    public function setSerializer(SerializerInterface $serializer): self;
}