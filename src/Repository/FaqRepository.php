<?php

declare(strict_types=1);

namespace Module\Faq\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;

class FaqRepository extends EntityRepository
{
    /**
     * Returns all active FAQ categories with their active questions for a given language,
     * ordered by category position then question position.
     *
     * @return list<array{name: string, icon: string, questions: list<array{question: string, answer: string}>}>
     */
    public static function getFrontCategories(int $idLang): array
    {
        $prefix = _DB_PREFIX_;

        $sql = "
            SELECT
                c.id_faq_category,
                c.icon,
                cl.name AS category_name,
                fl.question,
                fl.answer
            FROM `{$prefix}faq_category` c
            INNER JOIN `{$prefix}faq_category_lang` cl
                ON cl.id_faq_category = c.id_faq_category
                AND cl.id_lang = {$idLang}
            INNER JOIN `{$prefix}faq` f
                ON f.id_faq_category = c.id_faq_category
                AND f.active = 1
            INNER JOIN `{$prefix}faq_lang` fl
                ON fl.id_faq = f.id_faq
                AND fl.id_lang = {$idLang}
            WHERE c.active = 1
            ORDER BY c.position ASC, f.position ASC
        ";

        $rows = \Db::getInstance()->executeS($sql);

        $categories = [];
        foreach ($rows as $row) {
            $catId = (int) $row['id_faq_category'];
            if (!isset($categories[$catId])) {
                $categories[$catId] = [
                    'name' => $row['category_name'],
                    'icon' => $row['icon'],
                    'questions' => [],
                ];
            }
            $categories[$catId]['questions'][] = [
                'question' => $row['question'],
                'answer' => $row['answer'],
            ];
        }

        return array_values($categories);
    }

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
