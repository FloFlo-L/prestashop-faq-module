<?php

declare(strict_types=1);

namespace Module\Faq\Database;

use Doctrine\ORM\EntityManagerInterface;
use Module\Faq\Entity\Faq;
use Module\Faq\Entity\FaqLang;
use Module\Faq\Repository\FaqCategoryRepository;
use Module\Faq\Repository\FaqRepository;
use PrestaShopBundle\Entity\Repository\LangRepository;

class FaqGenerator
{
    public function __construct(
        private readonly FaqRepository $faqRepository,
        private readonly FaqCategoryRepository $faqCategoryRepository,
        private readonly LangRepository $langRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function generateQuestions(): void
    {
        $this->removeAllQuestions();
        $this->insertQuestions();
    }

    private function removeAllQuestions(): void
    {
        $faqs = $this->faqRepository->findAll();
        foreach ($faqs as $faq) {
            $this->entityManager->remove($faq);
        }
        $this->entityManager->flush();
    }

    private function insertQuestions(): void
    {
        $languages = $this->langRepository->findAll();
        $categories = $this->faqCategoryRepository->findBy([], ['position' => 'ASC']);

        $fixtureFile = __DIR__ . '/../../Resources/data/fixtures/faq_questions.json';
        $questionsData = json_decode(file_get_contents($fixtureFile), true);

        $globalPosition = 0;

        foreach ($questionsData as $questionData) {
            $categoryIndex = $questionData['category_index'];

            if (!isset($categories[$categoryIndex])) {
                continue;
            }

            $faq = new Faq();
            $faq->setFaqCategory($categories[$categoryIndex]);
            $faq->setPosition($globalPosition++);
            $faq->setActive($questionData['active']);

            foreach ($languages as $language) {
                $isoCode = $language->getIsoCode();
                $translation = $questionData['translations'][$isoCode]
                    ?? $questionData['translations']['en'];

                $faqLang = new FaqLang();
                $faqLang->setLang($language);
                $faqLang->setQuestion($translation['question']);
                $faqLang->setAnswer($translation['answer']);

                $faq->addFaqLang($faqLang);
            }

            $this->entityManager->persist($faq);
        }

        $this->entityManager->flush();
    }
}
