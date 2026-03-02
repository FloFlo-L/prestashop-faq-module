<?php

declare(strict_types=1);

namespace Module\Faq\Controller\Admin;

use Module\Faq\Database\FaqCategoryGenerator;
use Module\Faq\Database\FaqGenerator;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqGenerateController extends PrestaShopAdminController
{
    /**
     * This method displays the demo data generation page and processes the generation on POST.
     *
     * @param Request $request
     * @param FaqCategoryGenerator $categoryGenerator
     * @param FaqGenerator $faqGenerator
     *
     * @return RedirectResponse|Response
     */
    public function generate(Request $request, FaqCategoryGenerator $categoryGenerator, FaqGenerator $faqGenerator): Response
    {
        $allowedRoutes = ['faq_category_index', 'faq_question_index'];

        if ($request->isMethod(Request::METHOD_POST)) {
            $categoryGenerator->generateCategories();
            $faqGenerator->generateQuestions();
            $this->addFlash('success', $this->trans('Demo data were successfully generated.', [], 'Modules.Faq.Admin'));

            $redirectRoute = $request->request->get('redirect', 'faq_category_index');
            if (!in_array($redirectRoute, $allowedRoutes, true)) {
                $redirectRoute = 'faq_category_index';
            }

            return $this->redirectToRoute($redirectRoute);
        }

        $redirectRoute = $request->query->get('redirect', 'faq_category_index');
        if (!in_array($redirectRoute, $allowedRoutes, true)) {
            $redirectRoute = 'faq_category_index';
        }

        return $this->render('@Modules/faq/views/templates/admin/generate.html.twig', [
            'layoutTitle' => $this->trans('Generate demo data', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
            'redirectRoute' => $redirectRoute,
        ]);
    }

    /**
     * This method returns the toolbar buttons for the generate page.
     *
     * @return array
     */
    private function getToolbarButtons(): array
    {
        return [
            'categories' => [
                'href' => $this->generateUrl('faq_category_index'),
                'desc' => $this->trans('List categories', [], 'Modules.Faq.Admin'),
                'icon' => 'list',
                'class' => 'btn-outline-secondary',
            ],
            'questions' => [
                'href' => $this->generateUrl('faq_question_index'),
                'desc' => $this->trans('List questions', [], 'Modules.Faq.Admin'),
                'icon' => 'list',
                'class' => 'btn-outline-secondary',
            ],
        ];
    }
}
