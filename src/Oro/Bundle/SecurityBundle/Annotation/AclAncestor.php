<?php

namespace Oro\Bundle\SecurityBundle\Annotation;

/**
 * @Annotation
 * @Target({"METHOD", "CLASS"})
 */
class AclAncestor implements \Serializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * Constructor
     *
     * @param  array                     $data
     * @throws \InvalidArgumentException
     */
    public function __construct(array $data)
    {
        $this->id = isset($data['value']) ? $data['value'] : null;
        if (empty($this->id) || strpos($this->id, ' ') !== false) {
            throw new \InvalidArgumentException('ACL id must not be empty or contain blank spaces.');
        }
    }

    /**
     * Gets id of ACL annotation this ancestor is referred to
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize(
            [
                $this->id
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        list(
            $this->id
            ) = unserialize($serialized);
    }
}
