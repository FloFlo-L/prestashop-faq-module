<?php

declare(strict_types=1);

namespace Module\Faq\Form\ChoiceProvider;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides the list of active FAQ categories as choices for the question form select.
 */
class FaqCategoryChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly int $idLang,
    ) {
    }

    /**
     * Returns choices as ['Category name' => id_faq_category].
     */
    public function getChoices(): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('fc.id_faq_category, fcl.name')
            ->from($this->dbPrefix . 'faq_category', 'fc')
            ->innerJoin('fc', $this->dbPrefix . 'faq_category_lang', 'fcl',
                'fc.id_faq_category = fcl.id_faq_category AND fcl.id_lang = :idLang'
            )
            ->where('fc.active = 1')
            ->orderBy('fc.position', 'ASC')
            ->setParameter('idLang', $this->idLang)
            ->executeQuery()
            ->fetchAllAssociative();

        $choices = [];
        foreach ($rows as $row) {
            $choices[$row['name']] = $row['id_faq_category'];
        }

        return $choices;
    }
}
