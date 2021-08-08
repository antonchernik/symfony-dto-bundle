<?php

declare(strict_types=1);

namespace DTOBundle\Mapper;

interface AutoMapperAwareInterface
{
    public function convert(array|object $source, object|string|array $destination, array $context = []): mixed;
    public function convertCollection(iterable $sources, string $destination, array $context = []): iterable;
}
