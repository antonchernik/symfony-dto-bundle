<?php

declare(strict_types=1);

namespace DTOBundle\Trait;

use Symfony\Component\Serializer\SerializerInterface;

trait SerializerTrait
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @return SerializerInterface
     */
    public function getSerializer(): SerializerInterface
    {
        return $this->serializer;
    }

    /**
     * @required
     * @param SerializerInterface $serializer
     *
     * @return $this
     */
    public function setSerializer(SerializerInterface $serializer): self
    {
        $this->serializer = $serializer;

        return $this;
    }
}
