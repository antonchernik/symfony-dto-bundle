<?php

declare(strict_types=1);

namespace DTOBundle\Mapper;

use AutoMapperPlus\DataType;
use AutoMapperPlus\MappingOperation\Operation;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Exception\UnregisteredMappingException;

class AutoMapper implements AutoMapperAwareInterface
{
    public function __construct(
        protected AutoMapperInterface $autoMapper,
        protected PropertyInfoExtractorInterface $extractor
    ) {}

    /**
     * @throws UnregisteredMappingException
     */
    public function convert(array|object $source, array|object|string $destination, array $context = []): mixed
    {
        $this->autoConfiguration($source, $destination);
        if (is_object($destination)) {
            return $this->autoMapper->mapToObject($source, $destination, $context);
        }

        return $this->autoMapper->map($source, $destination, $context);
    }

    public function convertCollection(iterable $sources, string $destination, array $context = []): iterable
    {
        if (empty($sources)) {
            return [];
        }
        $this->autoConfiguration(end($sources), $destination);

        return $this->autoMapper->mapMultiple($sources, $destination, $context);
    }

    protected function autoConfiguration(array|object $source, array|object|string $destination): void
    {
        $destination = is_object($destination) ? get_class($destination) : $destination;
        if (!is_array($source) ||
            $this->autoMapper->getConfiguration()->hasMappingFor('array', $destination)
        ) {
            return;
        }

        $this->createSchemaForMapping($destination);
    }

    /**
     * @param string $destination
     */
    protected function createSchemaForMapping(string $destination): void
    {
        $config = $this->autoMapper->getConfiguration();
        $mapping = $config->registerMapping(DataType::ARRAY, $destination);
        if (null !== $mapping || !$props = $this->extractor->getProperties($destination)) {
            return;
        }

        foreach ($props as $property) {
            if (!$types = $this->extractor->getTypes($destination, $property)) {
                continue;
            }
            /** @var null|Type $propertyInfo */
            $propertyInfo = $types[0] ?? null;
            if (
                !empty($propertyInfo->getCollectionValueTypes()[0]) &&
                $propertyInfo->getCollectionValueTypes()[0]->getClassName()
            ) {
                $innerClass = $propertyInfo->getCollectionValueTypes()[0]->getClassName();
                $this->createSchemaForMapping($innerClass);
                $mapping->forMember($property, Operation::mapTo($innerClass));
            } elseif ($propertyInfo->getBuiltinType() === 'object') {
                $innerClass = $propertyInfo->getClassName();
                $this->createSchemaForMapping($innerClass);
                $mapping->forMember($property, Operation::mapTo($innerClass, true));
            }
        }
    }
}
