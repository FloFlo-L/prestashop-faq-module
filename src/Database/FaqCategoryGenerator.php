<?php

declare(strict_types=1);

namespace Module\Faq\Database;

use Doctrine\ORM\EntityManagerInterface;
use Module\Faq\Entity\FaqCategory;
use Module\Faq\Entity\FaqCategoryLang;
use Module\Faq\Repository\FaqCategoryRepository;
use PrestaShopBundle\Entity\Repository\LangRepository;

class FaqCategoryGenerator
{
    public function __construct(
        private readonly FaqCategoryRepository $faqCategoryRepository,
        private readonly LangRepository $langRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function generateCategories(): void
    {
        $this->removeAllCategories();
        $this->insertCategories();
    }

    private function removeAllCategories(): void
    {
        $categories = $this->faqCategoryRepository->findAll();
        foreach ($categories as $category) {
            $this->entityManager->remove($category);
        }
        $this->entityManager->flush();
    }

    private function insertCategories(): void
    {
        $languages = $this->langRepository->findAll();

        $fixtureFile = __DIR__ . '/../../Resources/data/fixtures/faq_categories.json';
        $categoriesData = json_decode(file_get_contents($fixtureFile), true);

        foreach ($categoriesData as $categoryData) {
            $category = new FaqCategory();
            $category->setIcon($categoryData['icon']);
            $category->setPosition($categoryData['position']);
            $category->setActive($categoryData['active']);

            foreach ($languages as $language) {
                $categoryLang = new FaqCategoryLang();
                $categoryLang->setLang($language);

                if (isset($categoryData['translations'][$language->getIsoCode()])) {
                    $categoryLang->setName($categoryData['translations'][$language->getIsoCode()]);
                } else {
                    $categoryLang->setName($categoryData['translations']['en']);
                }

                $category->addFaqCategoryLang($categoryLang);
            }

            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();
    }
}
