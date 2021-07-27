<?php

declare(strict_types=1);

namespace DTOBundle\Mapper;

interface AutoMapperAwareInterface
{
    /**
     * @param array|object        $source
     * @param object|string|array $destination
     * @param array               $context
     *
     * @return mixed
     */
    public function convert($source, $destination, array $context = []): mixed;

    /**
     * @param iterable $sources
     * @param string   $destination
     * @param array    $context
     *
     * @return iterable
     */
    public function convertCollection(iterable $sources, string $destination, array $context = []): iterable;
}
