<?php

namespace Akeneo\Tool\Component\Classification\Model;

/**
 * Tag interface
 *
 * @author    Willy Mesnage <willy.mesnage@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface TagInterface
{
    /**
     * @return int|string
     */
    public function getId();

    /**
     * @param string $code
     */
    public function setCode(string $code): \Akeneo\Tool\Component\Classification\Model\TagInterface;

    public function getCode(): string;
}
