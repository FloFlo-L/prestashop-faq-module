<?php

declare(strict_types=1);

namespace Module\Faq\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;

class FaqRepository extends EntityRepository
{
    /**
     * Sets the active status of a FAQ question.
     *
     * @throws DatabaseException
     */
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
            throw new DatabaseException(sprintf('Could not update Faq #%d: %s', $id, $e->getMessage()));
        }
    }

    /**
     * Deletes a FAQ question and its translations (cascade handled by Doctrine).
     *
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
            throw new DatabaseException(sprintf('Could not delete Faq #%d: %s', $id, $e->getMessage()));
        }
    }

    private function reorderPositions(): void
    {
        $faqs = $this->findBy([], ['position' => 'ASC']);
        foreach ($faqs as $index => $faq) {
            $faq->setPosition($index);
        }
        $this->getEntityManager()->flush();
    }
}
