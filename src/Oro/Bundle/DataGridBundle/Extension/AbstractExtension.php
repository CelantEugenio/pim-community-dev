<?php

namespace Oro\Bundle\DataGridBundle\Extension;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datagrid\Common\MetadataIterableObject;
use Oro\Bundle\DataGridBundle\Datagrid\Common\ResultsIterableObject;
use Oro\Bundle\DataGridBundle\Datagrid\RequestParameters;
use Oro\Bundle\DataGridBundle\Datasource\DatasourceInterface;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Processor;

abstract class AbstractExtension implements ExtensionVisitorInterface
{
    /** @var RequestParameters */
    protected $requestParams;

    public function __construct(RequestParameters $requestParams = null)
    {
        $this->requestParams = $requestParams;
    }

    /**
     * {@inheritDoc}
     */
    public function processConfigs(DatagridConfiguration $config): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitDatasource(DatagridConfiguration $config, DatasourceInterface $datasource)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitMetadata(DatagridConfiguration $config, MetadataIterableObject $data)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function visitResult(DatagridConfiguration $config, ResultsIterableObject $result)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getPriority(): int
    {
        // default priority if not overridden by child
        return 0;
    }

    /**
     * Validate configuration
     *
     * @param ConfigurationInterface      $configuration
     * @param $config
     */
    protected function validateConfiguration(ConfigurationInterface $configuration, $config): array
    {
        $processor = new Processor();
        return $processor->processConfiguration(
            $configuration,
            $config
        );
    }

    /**
     * Getter for request parameters object
     */
    protected function getRequestParams(): \Oro\Bundle\DataGridBundle\Datagrid\RequestParameters
    {
        return $this->requestParams;
    }
}
