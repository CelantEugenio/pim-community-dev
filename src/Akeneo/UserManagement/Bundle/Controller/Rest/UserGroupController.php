<?php

namespace Akeneo\UserManagement\Bundle\Controller\Rest;

use Akeneo\UserManagement\Component\Repository\GroupRepositoryInterface;
use Akeneo\UserManagement\Bundle\Doctrine\ORM\Repository\GroupRepository;
use Akeneo\UserManagement\Component\Model\GroupInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * User group rest controller
 *
 * @author    Julien Sanchez <julien@akeneo.com>
 * @copyright 2016 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UserGroupController
{
    /** @var GroupRepository */
    protected $groupRepository;

    /**
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Get the list of all user groups
     *
     * @return JsonResponse all user groups
     */
    public function indexAction(): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $userGroups = array_map(fn(GroupInterface $group) => [
            'name' => $group->getName(),
            'meta' => [
                'id'      => $group->getId(),
                'default' => 'All' === $group->getName()
            ]
        ], $this->groupRepository->findAll());

        return new JsonResponse($userGroups);
    }
}
