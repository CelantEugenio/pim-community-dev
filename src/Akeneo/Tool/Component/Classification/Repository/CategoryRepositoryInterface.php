<?php

namespace Akeneo\Tool\Component\Classification\Repository;

use Akeneo\Tool\Component\Classification\Model\CategoryInterface;
use Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Gedmo\Tree\RepositoryInterface as TreeRepositoryInterface;

/**
 * Category repository interface
 *
 * @author    Julien Janvier <jjanvier@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface CategoryRepositoryInterface extends
    TreeRepositoryInterface,
    IdentifiableObjectRepositoryInterface,
    ObjectRepository
{
    /**
     * Get a collection of categories based on the array of id provided
     *
     * @param array $categoryIds
     *
     * @return Collection of categories
     */
    public function getCategoriesByIds(array $categoryIds = []): \Doctrine\Common\Collections\Collection;

    /**
     * Get a collection of categories based on the array of code provided
     *
     * @param array $categoryCodes
     *
     * @return Collection of categories
     */
    public function getCategoriesByCodes(array $categoryCodes = []): \Doctrine\Common\Collections\Collection;

    /**
     * Get a tree filled with children and their parents
     *
     * @param array $parentsIds parent ids
     */
    public function getTreeFromParents(array $parentsIds): array;

    /**
     * Shortcut to get all children ids
     *
     * @param CategoryInterface $parent      the parent
     * @param bool              $includeNode true to include actual node in query result
     *
     * @return integer[]
     */
    public function getAllChildrenIds(CategoryInterface $parent, bool $includeNode = false): array;

    /**
     * Shortcut to get all children codes
     *
     * @param CategoryInterface $parent      the parent
     * @param bool              $includeNode true to include actual node in query result
     *
     * @return string[]
     */
    public function getAllChildrenCodes(CategoryInterface $parent, bool $includeNode = false): array;

    /**
     * Return the categories IDs from their codes. The categories are not hydrated.
     *
     * @param array $codes
     */
    public function getCategoryIdsByCodes(array $codes): array;

    /**
     * Get children from a parent id
     *
     * @param int $parentId
     */
    public function getChildrenByParentId(int $parentId): ArrayCollection;

    /**
     * @param CategoryInterface $parent
     * @param array             $grantedCategoryIds
     */
    public function getChildrenGrantedByParentId(CategoryInterface $parent, array $grantedCategoryIds = []): array;

    /**
     * Get children tree from a parent id.
     * If the select node id is provided, the tree will be returned
     * down to the node specified by select node id. Otherwise, the
     * whole tree will be returned
     *
     * @param int  $parentId
     * @param bool $selectNodeId
     */
    public function getChildrenTreeByParentId(int $parentId, bool $selectNodeId = false, array $grantedCategoryIds = []): array;

    /**
     * Based on the Gedmo\Tree\RepositoryUtils\buildTreeArray, but with
     * keeping the node as object and able to manage nodes in different branches
     * (the original implementation works with only depth and associate all
     * nodes of depth D+1 to the last node of depth D.)
     *
     * @param array $nodes Must be sorted by increasing depth
     */
    public function buildTreeNode(array $nodes): array;

    /**
     * Get the Tree path of Nodes by given $node
     *
     * @param object $node
     *
     * @return array - list of Nodes in path
     */
    public function getPath(object $node): array;

    public function getTrees(): array;

    /**
     * Get trees of granted categories
     *
     * @param array $grantedCategoryIds
     */
    public function getGrantedTrees(array $grantedCategoryIds = []): array;

    /**
     * Check if a parent node is an ancestor of a child node
     *
     * @param CategoryInterface $parentNode
     * @param CategoryInterface $childNode
     */
    public function isAncestor(CategoryInterface $parentNode, CategoryInterface $childNode): bool;

    /**
     * Return the categories sorted by tree and ordered
     */
    public function getOrderedAndSortedByTreeCategories(): array;

    /**
     * Provides a tree filled up to the categories provided, with all their ancestors
     * and ancestors sibligns are filled too, in order to be able to display the tree
     * directly without loading other data.
     *
     * @param CategoryInterface $root       Tree root category
     * @param Collection        $categories Collection of categories
     *
     * @return array Multi-dimensional array representing the tree
     */
    public function getFilledTree(CategoryInterface $root, Collection $categories): array;

    /**
     * @see Gedmo\Tree\Entity\Repository
     */
    public function persistAsNextSiblingOf(CategoryInterface $node, CategoryInterface $sibling);

    /**
     * @see Gedmo\Tree\Entity\Repository
     */
    public function persistAsFirstChildOf(CategoryInterface $node, CategoryInterface $parent);
}
