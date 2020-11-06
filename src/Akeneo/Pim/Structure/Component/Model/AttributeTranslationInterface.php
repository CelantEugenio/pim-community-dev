<?php

namespace Akeneo\Pim\Structure\Component\Model;

use Akeneo\Tool\Component\Localization\Model\TranslationInterface;

/**
 * Attribute translation interface
 *
 * @author    Marie Bochu <marie.bochu@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface AttributeTranslationInterface extends TranslationInterface
{
    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel(string $label): \Akeneo\Pim\Structure\Component\Model\AttributeTranslationInterface;

    /**
     * Get the label
     */
    public function getLabel(): string;
}
