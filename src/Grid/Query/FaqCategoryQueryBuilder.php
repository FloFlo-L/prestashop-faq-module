<?php

declare(strict_types=1);

namespace Module\Faq\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class FaqCategoryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly int $idLang,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(?SearchCriteriaInterface $searchCriteria = null): QueryBuilder
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria->getFilters());

        $qb->select('fc.id_faq_category, fcl.name, fc.position, fc.active, fc.date_add')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset() ?? 0)
            ->setMaxResults($searchCriteria->getLimit());

        return $qb;
    }

    public function getCountQueryBuilder(?SearchCriteriaInterface $searchCriteria = null): QueryBuilder
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT fc.id_faq_category)');

        return $qb;
    }

    private function getBaseQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'faq_category', 'fc')
            ->innerJoin(
                'fc',
                $this->dbPrefix . 'faq_category_lang',
                'fcl',
                'fc.id_faq_category = fcl.id_faq_category AND fcl.id_lang = :idLang'
            )
            ->setParameter('idLang', $this->idLang);

        foreach ($filters as $name => $value) {
            if ('name' === $name) {
                $qb->andWhere('fcl.name LIKE :name')
                    ->setParameter('name', '%' . $value . '%');
                continue;
            }

            if ('active' === $name) {
                $qb->andWhere('fc.active = :active')
                    ->setParameter('active', $value);
                continue;
            }
        }

        return $qb;
    }
}
