<?php

namespace Akeneo\Tool\Bundle\ElasticsearchBundle\IndexConfiguration;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Yaml\Parser;

/**
 * Elasticsearch configuration loader. Allows to load "index settings", "mappings" and "aliases".
 * To learn more, see {@link https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-create-index.html}
 *
 * This loader is able to load the configuration from several different files. For instance, from the default
 * Akeneo file, and from a custom project file.
 *
 * @author    Julien Janvier <j.janvier@gmail.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Loader
{
    /** @var array */
    private $configurationFiles;

    /** @var ParameterBagInterface */
    private $parameterBag;

    /**
     * @param array $configurationFiles
     */
    public function __construct(array $configurationFiles, ParameterBagInterface $parameterBag)
    {
        $this->configurationFiles = $configurationFiles;
        $this->parameterBag = $parameterBag;
    }

    /**
     * Load the Elasticsearch index configuration from multiple YAML files.
     *
     * @throws \Exception
     */
    public function load(): IndexConfiguration
    {
        $settings = [];
        $mappings = [];
        $aliases = [];
        $yaml = new Parser();

        foreach ($this->configurationFiles as $configurationFile) {
            if (!is_readable($configurationFile)) {
                throw new \Exception(
                    sprintf('The elasticsearch configuration file "%s" is not readable.', $configurationFile)
                );
            }

            $configuration = $yaml->parse(file_get_contents($configurationFile));

            array_walk_recursive($configuration, function (&$value) {
                $value = $this->parameterBag->resolveValue($value);
            });

            if (isset($configuration['settings'])) {
                $settings = array_replace_recursive($settings, $configuration['settings']);
            }
            if (isset($configuration['mappings'])) {
                $mappings = $this->mergeMappings($mappings, $configuration['mappings']);
            }
            if (isset($configuration['aliases'])) {
                $aliases = array_replace_recursive($aliases, $configuration['aliases']);
            }
        }

        return new IndexConfiguration($settings, $mappings, $aliases);
    }

    /**
     * Mappings must be merged considering three cases:
     * - 'properties' is an associative array and new definitions must replace old ones if they have the same key
     * - 'dynamic_templates' is an indexed array and new definitions must always be added
     * - other keys, merged with array_replace policy
     */
    private function mergeMappings(array $originalMappings, array $additionalMappings): array
    {
        if (isset($additionalMappings['properties'])) {
            $originalProperties = $originalMappings['properties'] ?? [];

            $originalMappings['properties'] = array_replace_recursive(
                $originalProperties,
                $additionalMappings['properties']
            );
        }
        if (isset($additionalMappings['dynamic_templates'])) {
            $originalTemplates = $originalMappings['dynamic_templates'] ?? [];

            $originalMappings['dynamic_templates'] = array_merge_recursive(
                $originalTemplates,
                $additionalMappings['dynamic_templates']
            );
        }
        // hacky stuff to merge all other mappings
        $otherMappings = $additionalMappings;
        unset($otherMappings['properties']);
        unset($otherMappings['dynamic_templates']);

        return array_replace_recursive(
            $originalMappings,
            $otherMappings
        );
    }
}
