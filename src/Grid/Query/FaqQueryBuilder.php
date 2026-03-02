<?php

declare(strict_types=1);

namespace Module\Faq\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class FaqQueryBuilder extends AbstractDoctrineQueryBuilder
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

        $qb->select('f.id_faq, fcl.name AS category_name, fl.question, f.position, f.active')
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
        $qb->select('COUNT(DISTINCT f.id_faq)');

        return $qb;
    }

    private function getBaseQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'faq', 'f')
            ->innerJoin('f', $this->dbPrefix . 'faq_lang', 'fl',
                'f.id_faq = fl.id_faq AND fl.id_lang = :idLang'
            )
            ->innerJoin('f', $this->dbPrefix . 'faq_category', 'fc',
                'f.id_faq_category = fc.id_faq_category'
            )
            ->innerJoin('fc', $this->dbPrefix . 'faq_category_lang', 'fcl',
                'fc.id_faq_category = fcl.id_faq_category AND fcl.id_lang = :idLang'
            )
            ->setParameter('idLang', $this->idLang);

        foreach ($filters as $name => $value) {
            if ('question' === $name) {
                $qb->andWhere('fl.question LIKE :question')
                    ->setParameter('question', '%' . $value . '%');
                continue;
            }

            if ('id_faq_category' === $name) {
                $qb->andWhere('f.id_faq_category = :id_faq_category')
                    ->setParameter('id_faq_category', $value);
                continue;
            }

            if ('active' === $name) {
                $qb->andWhere('f.active = :active')
                    ->setParameter('active', $value);
                continue;
            }
        }

        return $qb;
    }
}
