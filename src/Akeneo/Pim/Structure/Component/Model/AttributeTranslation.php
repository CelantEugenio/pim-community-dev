<?php

namespace Akeneo\Pim\Structure\Component\Model;

use Akeneo\Tool\Component\Localization\Model\AbstractTranslation;

/**
 * Attribute translation entity
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeTranslation extends AbstractTranslation implements AttributeTranslationInterface
{
    /** All required columns are mapped through inherited superclass */

    /** Change foreign key to add constraint and work with basic entity */
    protected $foreignKey;

    /** @var string */
    protected $label;

    /**
     * {@inheritdoc}
     */
    public function setLabel(string $label): AttributeTranslationInterface
    {
        $this->label = $label;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return $this->label;
    }
}
