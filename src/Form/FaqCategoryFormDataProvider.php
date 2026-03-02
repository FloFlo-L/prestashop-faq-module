<?php

declare(strict_types=1);

namespace Module\Faq\Form;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class FaqCategoryFormDataProvider implements FormDataProviderInterface
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
                'active' => true,
                'icon' => '',
            ];
        }

        $category = $this->connection->createQueryBuilder()
            ->select('fc.icon, fc.active')
            ->from($this->dbPrefix . 'faq_category', 'fc')
            ->where('fc.id_faq_category = :id')
            ->setParameter('id', $this->id)
            ->executeQuery()
            ->fetchAssociative();

        if (!$category) {
            return ['active' => true, 'icon' => ''];
        }

        $langs = $this->connection->createQueryBuilder()
            ->select('fcl.id_lang, fcl.name')
            ->from($this->dbPrefix . 'faq_category_lang', 'fcl')
            ->where('fcl.id_faq_category = :id')
            ->setParameter('id', $this->id)
            ->executeQuery()
            ->fetchAllAssociative();

        $name = [];
        foreach ($langs as $lang) {
            $name[(int) $lang['id_lang']] = $lang['name'];
        }

        return [
            'name' => $name,
            'icon' => $category['icon'],
            'active' => (bool) $category['active'],
        ];
    }

    public function toggleActive(int $id): void
    {
        $current = (int) $this->connection->createQueryBuilder()
            ->select('fc.active')
            ->from($this->dbPrefix . 'faq_category', 'fc')
            ->where('fc.id_faq_category = :id')
            ->setParameter('id', $id)
            ->executeQuery()
            ->fetchOne();

        $this->connection->update($this->dbPrefix . 'faq_category', [
            'active' => $current ? 0 : 1,
        ], ['id_faq_category' => $id]);
    }

    public function setData(array $data): array
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        if ($this->id === null) {
            $maxPosition = (int) $this->connection->createQueryBuilder()
                ->select('COALESCE(MAX(fc.position), -1)')
                ->from($this->dbPrefix . 'faq_category', 'fc')
                ->executeQuery()
                ->fetchOne();

            $this->connection->insert($this->dbPrefix . 'faq_category', [
                'icon' => $data['icon'] ?? '',
                'position' => $maxPosition + 1,
                'active' => (int) ($data['active'] ?? true),
                'date_add' => $now,
                'date_upd' => $now,
            ]);

            $this->id = (int) $this->connection->lastInsertId();
        } else {
            $this->connection->update($this->dbPrefix . 'faq_category', [
                'icon' => $data['icon'] ?? '',
                'active' => (int) ($data['active'] ?? true),
                'date_upd' => $now,
            ], ['id_faq_category' => $this->id]);
        }

        foreach ($data['name'] as $langId => $name) {
            $existing = $this->connection->createQueryBuilder()
                ->select('1')
                ->from($this->dbPrefix . 'faq_category_lang', 'fcl')
                ->where('fcl.id_faq_category = :id AND fcl.id_lang = :langId')
                ->setParameter('id', $this->id)
                ->setParameter('langId', $langId)
                ->executeQuery()
                ->fetchOne();

            if ($existing) {
                $this->connection->update($this->dbPrefix . 'faq_category_lang', [
                    'name' => $name ?? '',
                ], ['id_faq_category' => $this->id, 'id_lang' => $langId]);
            } else {
                $this->connection->insert($this->dbPrefix . 'faq_category_lang', [
                    'id_faq_category' => $this->id,
                    'id_lang' => $langId,
                    'name' => $name ?? '',
                ]);
            }
        }

        return [];
    }
}
