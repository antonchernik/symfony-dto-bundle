<?php

declare(strict_types=1);

namespace DTOBundle\Mapper;

use AutoMapperPlus\DataType;
use AutoMapperPlus\MappingOperation\Operation;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use AutoMapperPlus\AutoMapperInterface;
use AutoMapperPlus\Exception\UnregisteredMappingException;

class AutoAutoMapperAware implements AutoMapperAwareInterface
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
        if (null !== $config->getMappingFor(DataType::ARRAY, $destination)) {
            return;
        }
        $mapping = $config->registerMapping('array', $destination);
        $props = $this->extractor->getProperties($destination);
        foreach ($props as $property) {
            /** @var Type $propertyInfo */
            $types = $this->extractor->getTypes($destination, $property);
            if (!$types) {
                continue;
            }
            $propertyInfo = $types[0];
            $innerClass = false;
            if (!empty($propertyInfo->getCollectionValueTypes()[0])) {
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
