<?php

declare(strict_types=1);

namespace Module\Faq\Controller\Admin;

use Module\Faq\Database\FaqCategoryGenerator;
use Module\Faq\Form\FaqCategoryFormDataProvider;
use Module\Faq\Form\FaqCategoryFormType;
use Module\Faq\Grid\Filters\FaqCategoryFilters;
use Module\Faq\Repository\FaqCategoryRepository;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqCategoryController extends PrestaShopAdminController
{
    public function __construct(
        private readonly GridFactoryInterface $faqCategoryGridFactory,
        private readonly FaqCategoryFormDataProvider $formDataProvider,
        private readonly FaqCategoryRepository $faqCategoryRepository,
    ) {
    }

    public function index(FaqCategoryFilters $filters): Response
    {
        $grid = $this->faqCategoryGridFactory->getGrid($filters);

        return $this->render('@Modules/faq/views/templates/admin/category/index.html.twig', [
            'grid' => $this->presentGrid($grid),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
        ]);
    }

    public function create(): Response
    {
        $form = $this->createForm(FaqCategoryFormType::class, $this->formDataProvider->getData());

        return $this->render('@Modules/faq/views/templates/admin/category/form.html.twig', [
            'faqCategoryForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_category_create_process'),
            'layoutTitle' => $this->trans('Add new category', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
        ]);
    }

    public function createProcess(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(FaqCategoryFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->formDataProvider->setData($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Category created successfully.', [], 'Modules.Faq.Admin'));

                return $this->redirectToRoute('faq_category_index');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->render('@Modules/faq/views/templates/admin/category/form.html.twig', [
            'faqCategoryForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_category_create_process'),
            'layoutTitle' => $this->trans('Add new category', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
        ]);
    }

    public function edit(int $faqCategoryId): Response
    {
        $this->formDataProvider->setId($faqCategoryId);
        $form = $this->createForm(FaqCategoryFormType::class, $this->formDataProvider->getData());

        return $this->render('@Modules/faq/views/templates/admin/category/form.html.twig', [
            'faqCategoryForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_category_edit_process', ['faqCategoryId' => $faqCategoryId]),
            'layoutTitle' => $this->trans('Edit category', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
        ]);
    }

    public function editProcess(int $faqCategoryId, Request $request): RedirectResponse|Response
    {
        $this->formDataProvider->setId($faqCategoryId);
        $form = $this->createForm(FaqCategoryFormType::class, $this->formDataProvider->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->formDataProvider->setData($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Category updated successfully.', [], 'Modules.Faq.Admin'));

                return $this->redirectToRoute('faq_category_index');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->render('@Modules/faq/views/templates/admin/category/form.html.twig', [
            'faqCategoryForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_category_edit_process', ['faqCategoryId' => $faqCategoryId]),
            'layoutTitle' => $this->trans('Edit category', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
        ]);
    }

    public function generate(Request $request, FaqCategoryGenerator $generator): Response
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            $generator->generateCategories();
            $this->addFlash('success', $this->trans('Categories were successfully generated.', [], 'Modules.Faq.Admin'));

            return $this->redirectToRoute('faq_category_index');
        }

        return $this->render('@Modules/faq/views/templates/admin/category/generate.html.twig', [
            'layoutTitle' => $this->trans('Generate demo categories', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
        ]);
    }

    public function delete(int $faqCategoryId): RedirectResponse
    {
        try {
            $this->faqCategoryRepository->delete($faqCategoryId);
            $this->addFlash('success', $this->trans('Category deleted successfully.', [], 'Modules.Faq.Admin'));
        } catch (DatabaseException $e) {
            $this->addFlashErrors([[
                'key' => 'Could not delete #%id%',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['%id%' => $faqCategoryId],
            ]]);
        }

        return $this->redirectToRoute('faq_category_index');
    }

    public function toggleActive(int $faqCategoryId): JsonResponse
    {
        $this->formDataProvider->toggleActive($faqCategoryId);

        return new JsonResponse([
            'status' => true,
            'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
        ]);
    }

    private function getToolbarButtons(): array
    {
        return [
            'add' => [
                'href' => $this->generateUrl('faq_category_create'),
                'desc' => $this->trans('Add new category', [], 'Modules.Faq.Admin'),
                'icon' => 'add_circle_outline',
            ],
            'generate' => [
                'href' => $this->generateUrl('faq_category_generate'),
                'desc' => $this->trans('Generate demo data', [], 'Modules.Faq.Admin'),
                'icon' => 'auto_fix_high',
                'class' => 'btn-outline-secondary',
            ],
        ];
    }

    private function getFormToolbarButtons(): array
    {
        return [
            'list' => [
                'href' => $this->generateUrl('faq_category_index'),
                'desc' => $this->trans('List categories', [], 'Modules.Faq.Admin'),
                'icon' => 'list',
                'class' => 'btn-outline-secondary',
            ],
        ];
    }
}
