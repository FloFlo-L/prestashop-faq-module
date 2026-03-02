<?php

declare(strict_types=1);

namespace Module\Faq\Form;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class FaqFormDataProvider implements FormDataProviderInterface
{
    private ?int $id = null;

    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getData(): array
    {
        if ($this->id === null) {
            return [
                'id_faq_category' => null,
                'active' => true,
            ];
        }

        $faq = $this->connection->createQueryBuilder()
            ->select('f.id_faq_category, f.active')
            ->from($this->dbPrefix . 'faq', 'f')
            ->where('f.id_faq = :id')
            ->setParameter('id', $this->id)
            ->executeQuery()
            ->fetchAssociative();

        if (!$faq) {
            return ['id_faq_category' => null, 'active' => true];
        }

        $langs = $this->connection->createQueryBuilder()
            ->select('fl.id_lang, fl.question, fl.answer')
            ->from($this->dbPrefix . 'faq_lang', 'fl')
            ->where('fl.id_faq = :id')
            ->setParameter('id', $this->id)
            ->executeQuery()
            ->fetchAllAssociative();

        $question = [];
        $answer = [];
        foreach ($langs as $lang) {
            $question[(int) $lang['id_lang']] = $lang['question'];
            $answer[(int) $lang['id_lang']] = $lang['answer'];
        }

        return [
            'id_faq_category' => (int) $faq['id_faq_category'],
            'question' => $question,
            'answer' => $answer,
            'active' => (bool) $faq['active'],
        ];
    }

    public function setData(array $data): array
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        if ($this->id === null) {
            $maxPosition = (int) $this->connection->createQueryBuilder()
                ->select('COALESCE(MAX(f.position), -1)')
                ->from($this->dbPrefix . 'faq', 'f')
                ->executeQuery()
                ->fetchOne();

            $this->connection->insert($this->dbPrefix . 'faq', [
                'id_faq_category' => (int) $data['id_faq_category'],
                'position' => $maxPosition + 1,
                'active' => (int) ($data['active'] ?? true),
                'date_add' => $now,
                'date_upd' => $now,
            ]);

            $this->id = (int) $this->connection->lastInsertId();
        } else {
            $this->connection->update($this->dbPrefix . 'faq', [
                'id_faq_category' => (int) $data['id_faq_category'],
                'active' => (int) ($data['active'] ?? true),
                'date_upd' => $now,
            ], ['id_faq' => $this->id]);
        }

        foreach ($data['question'] as $langId => $question) {
            $existing = $this->connection->createQueryBuilder()
                ->select('1')
                ->from($this->dbPrefix . 'faq_lang', 'fl')
                ->where('fl.id_faq = :id AND fl.id_lang = :langId')
                ->setParameter('id', $this->id)
                ->setParameter('langId', $langId)
                ->executeQuery()
                ->fetchOne();

            if ($existing) {
                $this->connection->update($this->dbPrefix . 'faq_lang', [
                    'question' => $question ?? '',
                    'answer' => $data['answer'][$langId] ?? null,
                ], ['id_faq' => $this->id, 'id_lang' => $langId]);
            } else {
                $this->connection->insert($this->dbPrefix . 'faq_lang', [
                    'id_faq' => $this->id,
                    'id_lang' => $langId,
                    'question' => $question ?? '',
                    'answer' => $data['answer'][$langId] ?? null,
                ]);
            }
        }

        return [];
    }
}
