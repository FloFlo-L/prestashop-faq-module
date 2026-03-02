<?php

declare(strict_types=1);

namespace Module\Faq\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;

class FaqCategoryRepository extends EntityRepository
{
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
        } catch (\Exception $e) {
            throw new DatabaseException(sprintf('Could not delete FaqCategory #%d: %s', $id, $e->getMessage()));
        }
    }
}