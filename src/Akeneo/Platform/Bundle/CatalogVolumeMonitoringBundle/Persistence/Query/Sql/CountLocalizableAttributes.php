<?php

declare(strict_types=1);

namespace Akeneo\Platform\Bundle\CatalogVolumeMonitoringBundle\Persistence\Query\Sql;

use Akeneo\Platform\Component\CatalogVolumeMonitoring\Volume\Query\CountQuery;
use Akeneo\Platform\Component\CatalogVolumeMonitoring\Volume\ReadModel\CountVolume;
use Doctrine\DBAL\Connection;

/**
 * @author    Elodie Raposo <elodie.raposo@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CountLocalizableAttributes implements CountQuery
{
    private const VOLUME_NAME = 'count_localizable_attributes';

    /** @var Connection */
    private $connection;

    /** @var int */
    private $limit;

    /**
     * @param Connection $connection
     */
    public function __construct(\Doctrine\DBAL\Driver\Connection $connection, int $limit)
    {
        $this->connection = $connection;
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(): CountVolume
    {
        $sql = <<<SQL
            SELECT COUNT(*) as count
            FROM pim_catalog_attribute 
            WHERE is_localizable = 1 AND is_scopable = 0;
SQL;
        $result = $this->connection->query($sql)->fetch();

        return new CountVolume((int) $result['count'], $this->limit, self::VOLUME_NAME);
    }
}
