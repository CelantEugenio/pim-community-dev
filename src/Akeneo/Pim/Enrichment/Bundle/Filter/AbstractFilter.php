<?php

namespace Akeneo\Pim\Enrichment\Bundle\Filter;

/**
 * Abstract filter
 *
 * @author    Julien Sanchez <julien@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractFilter implements CollectionFilterInterface, ObjectFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filterCollection($objects, string $type, array $options = []): array
    {
        $filteredObjects = [];

        foreach ($objects as $key => $object) {
            if (!$this->filterObject($object, $type, $options)) {
                $filteredObjects[$key] = $object;
            }
        }

        return $filteredObjects;
    }

    /**
     * {@inheritdoc}
     */
    public function filterObject($objects, string $type, array $options = []): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsObject($object, string $type, array $options = []): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsCollection($collection, string $type, array $options = []): bool
    {
        return ($collection instanceof \Traversable && $collection instanceof \ArrayAccess) || is_array($collection);
    }
}
