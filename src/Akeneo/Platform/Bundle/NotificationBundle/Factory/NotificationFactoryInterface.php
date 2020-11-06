<?php

namespace Akeneo\Platform\Bundle\NotificationBundle\Factory;

use Akeneo\Platform\Bundle\NotificationBundle\Entity\NotificationInterface;

/**
 * Notification factory interface
 *
 * @author    Yohan Blain <yohan.blain@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface NotificationFactoryInterface
{
    /**
     * Creates a notification
     *
     * @param mixed $notification
     */
    public function create($notification): NotificationInterface;

    /**
     * Does this factory support the specified type ?
     *
     * @param string $type
     */
    public function supports(string $type): bool;
}
