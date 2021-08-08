<?php

declare(strict_types=1);

namespace DTOBundle\Trait;

use Symfony\Component\Serializer\SerializerInterface;

trait SerializerTrait
{
    protected SerializerInterface $serializer;

    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @required
     */
    public function setSerializer(SerializerInterface $serializer): self
    {
        $this->serializer = $serializer;

        return $this;
    }
}
