<?php

namespace Akeneo\Pim\Enrichment\Component\Product\Connector\Step;

use Akeneo\Pim\Enrichment\Component\Product\Connector\Item\MassEdit\TemporaryFileCleaner;
use Akeneo\Tool\Component\Batch\Job\JobRepositoryInterface;
use Akeneo\Tool\Component\Batch\Model\StepExecution;
use Akeneo\Tool\Component\Batch\Step\AbstractStep;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * BatchBundle Step for standard mass edit products
 *
 * @author    Olivier Soulet <olivier.soulet@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MassEditStep extends AbstractStep
{
    /** @var TemporaryFileCleaner */
    protected $cleaner;

    /**
     * @param string                   $name
     * @param EventDispatcherInterface $eventDispatcher
     * @param JobRepositoryInterface   $jobRepository
     * @param TemporaryFileCleaner     $cleaner
     */
    public function __construct(
        string $name,
        EventDispatcherInterface $eventDispatcher,
        JobRepositoryInterface $jobRepository,
        TemporaryFileCleaner $cleaner
    ) {
        parent::__construct($name, $eventDispatcher, $jobRepository);
        $this->cleaner = $cleaner;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute(StepExecution $stepExecution): void
    {
        $this->cleaner->setStepExecution($stepExecution);
        $this->cleaner->execute();
    }

    public function getCleaner(): TemporaryFileCleaner
    {
        return $this->cleaner;
    }

    public function setCleaner(TemporaryFileCleaner $cleaner): self
    {
        $this->cleaner = $cleaner;

        return $this;
    }
}
