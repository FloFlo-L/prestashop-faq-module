<?php

declare(strict_types=1);

namespace Module\Faq\Controller\Admin;

use Module\Faq\Form\FaqCategoryFormDataProvider;
use Module\Faq\Form\FaqCategoryFormType;
use Module\Faq\Grid\Filters\FaqCategoryFilters;
use Module\Faq\Repository\FaqCategoryRepository;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqCategoryController extends PrestaShopAdminController
{
    use BulkActionsTrait;

    public function __construct(
        private readonly GridFactoryInterface $faqCategoryGridFactory,
        private readonly FaqCategoryFormDataProvider $formDataProvider,
        private readonly FaqCategoryRepository $faqCategoryRepository,
    ) {
    }

    /**
     * This method returns the list of FAQ categories.
     * @param FaqCategoryFilters $filters
      *
      * @return Response
     */
    public function index(FaqCategoryFilters $filters): Response
    {
        $grid = $this->faqCategoryGridFactory->getGrid($filters);

        return $this->render('@Modules/faq/views/templates/admin/category/index.html.twig', [
            'grid' => $this->presentGrid($grid),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
        ]);
    }

    /**
     * This method displays the form to create a new FAQ category.
     *
     * @return Response
     */
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

    /**
     * This method processes the form to create a new FAQ category.
     *
     * @return RedirectResponse|Response
     */
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

    /**
     * This method displays the form to edit an existing FAQ category.
     *
     * @return Response
     */
    public function edit(int $faqCategoryId): Response
    {
        $this->formDataProvider->setId($faqCategoryId);
        $form = $this->createForm(FaqCategoryFormType::class, $this->formDataProvider->getData());

        return $this->render('@Modules/faq/views/templates/admin/category/form.html.twig', [
            'faqCategoryForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_category_edit_process', ['faqCategoryId' => $faqCategoryId]),
            'layoutTitle' => $this->trans('Edit category', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
            'entityId' => $faqCategoryId,
        ]);
    }

    /**
     * This method processes the form to update an existing FAQ category.
     *
     * @param int $faqCategoryId
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
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
            'entityId' => $faqCategoryId,
        ]);
    }

    /**
     * This method deletes a single FAQ category from the grid.
     *
     * @param int $faqCategoryId
     *
     * @return RedirectResponse
     */
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

    /**
     * This method enables all selected FAQ categories in bulk.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function enableBulk(Request $request): RedirectResponse
    {
        return $this->toggleActiveBulk($request, true);
    }

    /**
     * This method disables all selected FAQ categories in bulk.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function disableBulk(Request $request): RedirectResponse
    {
        return $this->toggleActiveBulk($request, false);
    }

    /**
     * This method deletes all selected FAQ categories in bulk.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulk(Request $request): RedirectResponse
    {
        $ids = $this->getBulkActionIds($request, 'faq_category_bulk');
        $errors = [];

        foreach ($ids as $id) {
            try {
                $this->faqCategoryRepository->delete($id);
            } catch (DatabaseException) {
                $errors[] = [
                    'key' => 'Could not delete #%id%',
                    'domain' => 'Admin.Notifications.Error',
                    'parameters' => ['%id%' => $id],
                ];
            }
        }

        if (empty($errors)) {
            $this->addFlash(
                'success',
                $this->trans('The selection has been successfully deleted.', [], 'Admin.Notifications.Success')
            );
        } else {
            $this->addFlashErrors($errors);
        }

        return $this->redirectToRoute('faq_category_index');
    }

    /**
     * This method toggles the active status of a single FAQ category via an async call from the grid toggle column.
     *
     * @param int $faqCategoryId
     *
     * @return JsonResponse
     */
    public function toggleActive(int $faqCategoryId): JsonResponse
    {
        $this->formDataProvider->toggleActive($faqCategoryId);

        return new JsonResponse([
            'status' => true,
            'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * This method sets the active status on all selected FAQ categories and redirects to the index.
     *
     * @param Request $request
     * @param bool $active
     *
     * @return RedirectResponse
     */
    private function toggleActiveBulk(Request $request, bool $active): RedirectResponse
    {
        $ids = $this->getBulkActionIds($request, 'faq_category_bulk');

        foreach ($ids as $id) {
            $this->faqCategoryRepository->setActive($id, $active);
        }

        $this->addFlash(
            'success',
            $this->trans('The selection has been successfully updated.', [], 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('faq_category_index');
    }

    /**
     * This method returns the toolbar buttons for the grid index page.
     *
     * @return array
     */
    private function getToolbarButtons(): array
    {
        return [
            'add' => [
                'href' => $this->generateUrl('faq_category_create'),
                'desc' => $this->trans('Add new category', [], 'Modules.Faq.Admin'),
                'icon' => 'add_circle_outline',
            ],
            'generate' => [
                'href' => $this->generateUrl('faq_generate', ['redirect' => 'faq_category_index']),
                'desc' => $this->trans('Generate demo data', [], 'Modules.Faq.Admin'),
                'icon' => 'auto_fix_high',
                'class' => 'btn-outline-secondary',
            ],
        ];
    }

    /**
     * This method returns the toolbar buttons for the create and edit form pages.
     *
     * @return array
     */
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
