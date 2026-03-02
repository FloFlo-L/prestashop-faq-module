<?php

declare(strict_types=1);

namespace Module\Faq\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;

class FaqCategoryRepository extends EntityRepository
{
    public function setActive(int $id, bool $active): void
    {
        $entity = $this->find($id);

        if ($entity === null) {
            return;
        }

        $entity->setActive($active);

        try {
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            throw new DatabaseException(sprintf('Could not update FaqCategory #%d: %s', $id, $e->getMessage()));
        }
    }

    /**
     * @throws DatabaseException
     */
    public function delete(int $id): void
    {
        $entity = $this->find($id);

        if ($entity === null) {
            return;
        }

        try {
            $this->getEntityManager()->remove($entity);
            $this->getEntityManager()->flush();
            $this->reorderPositions();
        } catch (\Exception $e) {
            throw new DatabaseException(sprintf('Could not delete FaqCategory #%d: %s', $id, $e->getMessage()));
        }
    }

    private function reorderPositions(): void
    {
        $categories = $this->findBy([], ['position' => 'ASC']);
        foreach ($categories as $index => $category) {
            $category->setPosition($index);
        }
        $this->getEntityManager()->flush();
    }
}