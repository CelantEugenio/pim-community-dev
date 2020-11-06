<?php

namespace Akeneo\Platform\Bundle\AnalyticsBundle\Command;

use Akeneo\Platform\Bundle\AnalyticsBundle\Command\Style\SystemInfoStyle;
use Akeneo\Tool\Component\Analytics\ChainedDataCollector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Displays system information provided by the data collectors through command line.
 *
 * @author    Damien Carcel <damien.carcel@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class SystemInfoCommand extends Command
{
    protected static $defaultName = 'pim:system:information';

    /** @var TranslatorInterface */
    private $translator;

    /** @var ChainedDataCollector */
    private $chainedDataCollector;

    public function __construct(
        TranslatorInterface $translator,
        ChainedDataCollector $chainedDataCollector
    ) {
        parent::__construct();

        $this->translator = $translator;
        $this->chainedDataCollector = $chainedDataCollector;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Displays Akeneo PIM system information');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $systemInfoStyle = new SystemInfoStyle($input, $output);

        $systemInfoStyle->title($this->translator->trans('pim_analytics.system_info.title'));
        $systemInfoStyle->table([], $this->formatCollectedData($this->translator, $this->getCollectedData()));
        return 0;
    }

    /**
     * Gets all the collected data from the system.
     */
    protected function getCollectedData(): array
    {
        return $this->chainedDataCollector->collect('system_info_report');
    }

    /**
     * Formats the collected data to be ready to display by the Table component.
     *
     * @param TranslatorInterface $translator
     * @param array               $collectedData
     */
    protected function formatCollectedData(TranslatorInterface $translator, array $collectedData): array
    {
        $formattedData = [];

        foreach ($collectedData as $key => $data) {
            if (is_array($data)) {
                $data = implode(",\n", $data);
            }

            if (!empty($formattedData)) {
                $formattedData[] = new TableSeparator();
            }
            $formattedData[] = [$translator->trans('pim_analytics.info_type.'.$key), $data];
        }

        return $formattedData;
    }
}
