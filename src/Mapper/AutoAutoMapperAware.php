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
    /**
     * @var AutoMapperInterface
     */
    protected AutoMapperInterface $autoMapper;

    /**
     * @var PropertyInfoExtractorInterface
     */
    protected PropertyInfoExtractorInterface $extractor;

    /**
     * @param AutoMapperInterface   $autoMapper
     * @param PropertyInfoExtractorInterface $extractor
     */
    public function __construct(AutoMapperInterface $autoMapper, PropertyInfoExtractorInterface $extractor)
    {
        $this->autoMapper = $autoMapper;
        $this->extractor = $extractor;
    }

    /**
     * @param array|object $source
     * @param array|object|string $destination
     * @param array $context
     *
     * @return mixed
     *
     * @throws UnregisteredMappingException
     */
    public function convert($source, $destination, array $context = []): mixed
    {
        $this->autoConfiguration($source, $destination);
        if (is_object($destination)) {
            return $this->autoMapper->mapToObject($source, $destination, $context);
        }

        return $this->autoMapper->map($source, $destination, $context);
    }

    /**
     * @param iterable $sources
     * @param string $destination
     * @param array $context
     *
     * @return iterable
     */
    public function convertCollection(iterable $sources, string $destination, array $context = []): iterable
    {
        if (empty($sources)) {
            return [];
        }

        $this->autoConfiguration(end($sources), $destination);

        return $this->autoMapper->mapMultiple($sources, $destination, $context);
    }

    /**
     * @param array|object $source
     * @param array|object|string $destination
     */
    protected function autoConfiguration($source, $destination): void
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
