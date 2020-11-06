<?php

namespace Akeneo\Tool\Bundle\VersioningBundle\Event;

use Akeneo\Tool\Component\Versioning\Model\VersionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @deprecated Will be removed in 4.0
 *
 * @todo merge in master: remove this class
 *
 * Event sent before a version is about to be purged by the version purger
 *
 * @author    Samir Boulil <samir.boulil@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PrePurgeVersionEvent extends Event
{
    /** @var VersionInterface */
    protected $version;

    /**
     * @param VersionInterface $version
     */
    public function __construct(VersionInterface $version)
    {
        $this->version = $version;
    }

    public function getVersion(): \Akeneo\Tool\Component\Versioning\Model\VersionInterface
    {
        return $this->version;
    }
}
