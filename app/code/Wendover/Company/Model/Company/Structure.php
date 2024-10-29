<?php

namespace Wendover\Company\Model\Company;

use Magento\Company\Model\Company\Structure as CoreStructure;
use Magento\Company\Api\Data\StructureInterface;
use Magento\Company\Api\Data\StructureInterfaceFactory;
use Magento\Company\Api\TeamRepositoryInterface;
use Magento\Company\Model\ResourceModel\Structure\Tree;
use Magento\Company\Model\StructureRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Exception\LocalizedException;

class Structure extends CoreStructure
{
    private $tree;
    private $structureRepository;
    private $searchCriteriaBuilder;

    public function __construct(
        Tree $tree,
        StructureInterfaceFactory $structureFactory,
        StructureRepository $structureRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TeamRepositoryInterface $teamRepository,
        CustomerRepositoryInterface $customerRepositoryInterface
    ) {
        $this->tree = $tree;
        $this->structureRepository = $structureRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct(
            $tree,
            $structureFactory,
            $structureRepository,
            $searchCriteriaBuilder,
            $teamRepository,
            $customerRepositoryInterface
        );
    }

    public function moveCustomerStructure($sourceCustomerId, $targetCustomerId, $keepOld)
    {
        $sourceStructure = $this->getStructureByCustomerId($sourceCustomerId);
        $targetStructure = $this->getStructureByCustomerId($targetCustomerId);
        if ($sourceStructure && $targetStructure) {
            if (!$keepOld) {
                $builder = $this->searchCriteriaBuilder;
                $builder->addFilter(StructureInterface::PATH, $sourceStructure->getId() . '/%', 'like');
                $results = $this->structureRepository->getList($builder->create());
                foreach ($results->getItems() as $result) {
                    if ($result->getParentId() == $sourceStructure->getId()) {
                        $result->setParentId($targetStructure->getId());
                    }
                    $path = (string) $result->getPath();
                    $path = preg_replace(
                        '/^' . $sourceStructure->getId() . '\//',
                        $targetStructure->getId() . '/',
                        $path
                    );
                    $result->setPath($path);
                    try {
                        $this->structureRepository->save($result);
                    } catch (LocalizedException $e) {
                        throw new LocalizedException(__(
                            'Unable to move customer structure.'
                        ));
                    }
                }
            }

            $sourceChildren = $this->getAdminUserChildren($sourceCustomerId);
            $this->executeMoveNode($sourceStructure->getId(), $targetStructure->getId(), true);
            $sourceNode = $this->getTreeById($sourceStructure->getId());
            foreach ($sourceChildren as $sourceChild) {
                $this->updateSourceChildren($sourceChild, $sourceNode->getId());
            }
        }
    }

    private function updateSourceChildren(Node $child, $parentId)
    {
        $this->moveNode($child->getId(), $parentId);
        if ($this->isStructureIdExists((int)$child->getId())) {
            foreach ($child->getChildren() as $childNode) {
                $this->updateSourceChildren($childNode, $child->getId());
            }
        }
    }

    private function isStructureIdExists(int $structureId): bool
    {
        $builder = $this->searchCriteriaBuilder;
        $builder->addFilter(StructureInterface::STRUCTURE_ID, $structureId);
        $results = $this->structureRepository->getList($builder->create());

        return !empty($results->getItems());
    }

    private function getAdminUserChildren($sourceCustomerId)
    {
        $children = [];
        $node = $this->getTreeByCustomerId($sourceCustomerId);
        /** @var Node $childNode */
        foreach ($node->getChildren() as $childNode) {
            $children[] = $childNode;
        }

        return $children;
    }

    private function executeMoveNode($id, $newParentId, $changeSuperUser)
    {
        $node = $this->getTreeById($id);
        $newParent = $this->getTreeById($newParentId);
        $this->checkIfNodeMoveIsPossible($node, $newParent, $changeSuperUser);
        $this->tree->move($node, $newParent);
    }

    private function checkIfNodeMoveIsPossible(
        Node $node,
        Node $newParent,
             $changeSuperUser
    ) {
        if ($changeSuperUser === false && !$node->getData(StructureInterface::PARENT_ID)) {
            throw new LocalizedException(__(
                'The company admin cannot be moved to a different location in the company structure.'
            ));
        }
        if ($node->getId() == $newParent->getId()) {
            throw new LocalizedException(__(
                'A user or a team cannot be moved under itself.'
            ));
        }
        $this->treeWalk($node, function (Node $childNode) use ($newParent) {
            if ($newParent->getId() == $childNode->getId()) {
                throw new LocalizedException(__(
                    'A user or a team cannot be moved under its child user or team.'
                ));
            }
        });

        if (!$changeSuperUser) {
            $rootItemId = $this->getFirstItemFromPath($node->getData(StructureInterface::PATH));
            $tree = $this->getTreeById($rootItemId);
            $isCompanyNode = false;
            $this->treeWalk(
                $tree,
                function (Node $childNode) use ($newParent, &$isCompanyNode) {
                    if ($newParent->getId() == $childNode->getId()) {
                        $isCompanyNode = true;
                    }
                }
            );
            if (!$isCompanyNode) {
                throw new LocalizedException(__(
                    'The specified parent ID belongs to a different company.'
                    . ' The specified entity (team or user) and its new parent must belong to the same company.'
                ));
            }
        }
    }

    private function treeWalk(
        Node $tree,
        callable $callback
    ) {
        if ($tree->hasChildren()) {
            /** @var Node $child */
            foreach ($tree->getChildren() as $child) {
                $this->treeWalk($child, $callback);
            }
        }
        // phpcs:ignore Magento2.Functions.DiscouragedFunction
        return call_user_func($callback, $tree);
    }
}
