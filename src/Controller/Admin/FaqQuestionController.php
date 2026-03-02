<?php

declare(strict_types=1);

namespace Module\Faq\Controller\Admin;

use Module\Faq\Form\FaqFormDataProvider;
use Module\Faq\Form\FaqFormType;
use Module\Faq\Grid\Filters\FaqFilters;
use Module\Faq\Repository\FaqRepository;
use PrestaShop\PrestaShop\Core\Exception\DatabaseException;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Controller\BulkActionsTrait;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqQuestionController extends PrestaShopAdminController
{
    use BulkActionsTrait; // BulkActionsTrait::getBulkActionIds()

    public function __construct(
        private readonly GridFactoryInterface $faqGridFactory,
        private readonly GridDefinitionFactoryInterface $faqGridDefinitionFactory,
        private readonly FaqRepository $faqRepository,
        private readonly FaqFormDataProvider $formDataProvider,
    ) {
    }

    /**
     * This method returns the list of FAQ questions.
     *
     * @param FaqFilters $filters
     *
     * @return Response
     */
    public function index(FaqFilters $filters): Response
    {
        $grid = $this->faqGridFactory->getGrid($filters);

        return $this->render('@Modules/faq/views/templates/admin/question/index.html.twig', [
            'grid' => $this->presentGrid($grid),
            'layoutHeaderToolbarBtn' => $this->getToolbarButtons(),
        ]);
    }

    /**
     * This method handles the search/filter form submission and redirects to the grid.
     */
    public function search(Request $request, ResponseBuilder $responseBuilder): RedirectResponse
    {
        return $responseBuilder->buildSearchResponse(
            $this->faqGridDefinitionFactory,
            $request,
            'faq',
            'faq_question_index'
        );
    }

    /**
     * This method displays the form to create a new FAQ question.
     *
     * @return Response
     */
    public function create(): Response
    {
        $form = $this->createForm(FaqFormType::class, $this->formDataProvider->getData());

        return $this->render('@Modules/faq/views/templates/admin/question/form.html.twig', [
            'faqForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_question_create_process'),
            'layoutTitle' => $this->trans('Add new question', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
        ]);
    }

    /**
     * This method processes the form to create a new FAQ question.
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createProcess(Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(FaqFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->formDataProvider->setData($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Question created successfully.', [], 'Modules.Faq.Admin'));

                return $this->redirectToRoute('faq_question_index');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->render('@Modules/faq/views/templates/admin/question/form.html.twig', [
            'faqForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_question_create_process'),
            'layoutTitle' => $this->trans('Add new question', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
        ]);
    }

    /**
     * This method displays the form to edit an existing FAQ question.
     *
     * @param int $faqId
     *
     * @return Response
     */
    public function edit(int $faqId): Response
    {
        $this->formDataProvider->setId($faqId);
        $form = $this->createForm(FaqFormType::class, $this->formDataProvider->getData());

        return $this->render('@Modules/faq/views/templates/admin/question/form.html.twig', [
            'faqForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_question_edit_process', ['faqId' => $faqId]),
            'layoutTitle' => $this->trans('Edit question', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
        ]);
    }

    /**
     * This method processes the form to update an existing FAQ question.
     *
     * @param int $faqId
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function editProcess(int $faqId, Request $request): RedirectResponse|Response
    {
        $this->formDataProvider->setId($faqId);
        $form = $this->createForm(FaqFormType::class, $this->formDataProvider->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $this->formDataProvider->setData($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Question updated successfully.', [], 'Modules.Faq.Admin'));

                return $this->redirectToRoute('faq_question_index');
            }

            foreach ($errors as $error) {
                $this->addFlash('error', $error);
            }
        }

        return $this->render('@Modules/faq/views/templates/admin/question/form.html.twig', [
            'faqForm' => $form->createView(),
            'formAction' => $this->generateUrl('faq_question_edit_process', ['faqId' => $faqId]),
            'layoutTitle' => $this->trans('Edit question', [], 'Modules.Faq.Admin'),
            'layoutHeaderToolbarBtn' => $this->getFormToolbarButtons(),
        ]);
    }

    /**
     * This method deletes a single FAQ question from the grid.
     *
     * @param int $faqId
     *
     * @return RedirectResponse
     */
    public function delete(int $faqId): RedirectResponse
    {
        try {
            $this->faqRepository->delete($faqId);
            $this->addFlash('success', $this->trans('Question deleted successfully.', [], 'Modules.Faq.Admin'));
        } catch (DatabaseException $e) {
            $this->addFlashErrors([[
                'key' => 'Could not delete #%id%',
                'domain' => 'Admin.Notifications.Error',
                'parameters' => ['%id%' => $faqId],
            ]]);
        }

        return $this->redirectToRoute('faq_question_index');
    }

    /**
     * This method toggles the active status of a single FAQ question via an async call from the grid toggle column.
     *
     * @param int $faqId
     *
     * @return JsonResponse
     */
    public function toggleActive(int $faqId): JsonResponse
    {
        $entity = $this->faqRepository->find($faqId);

        if ($entity !== null) {
            $this->faqRepository->setActive($faqId, !$entity->isActive());
        }

        return new JsonResponse([
            'status' => true,
            'message' => $this->trans('The status has been successfully updated.', [], 'Admin.Notifications.Success'),
        ]);
    }

    /**
     * This method enables all selected FAQ questions in bulk.
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
     * This method disables all selected FAQ questions in bulk.
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
     * This method deletes all selected FAQ questions in bulk.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function deleteBulk(Request $request): RedirectResponse
    {
        $ids = $this->getBulkActionIds($request, 'faq_bulk');
        $errors = [];

        foreach ($ids as $id) {
            try {
                $this->faqRepository->delete($id);
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

        return $this->redirectToRoute('faq_question_index');
    }

    /**
     * This method sets the active status on all selected FAQ questions and redirects to the index.
     *
     * @param Request $request
     * @param bool $active
     *
     * @return RedirectResponse
     */
    private function toggleActiveBulk(Request $request, bool $active): RedirectResponse
    {
        $ids = $this->getBulkActionIds($request, 'faq_bulk');

        foreach ($ids as $id) {
            $this->faqRepository->setActive($id, $active);
        }

        $this->addFlash(
            'success',
            $this->trans('The selection has been successfully updated.', [], 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('faq_question_index');
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
                'href' => $this->generateUrl('faq_question_index'),
                'desc' => $this->trans('List questions', [], 'Modules.Faq.Admin'),
                'icon' => 'list',
                'class' => 'btn-outline-secondary',
            ],
        ];
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
                'href' => $this->generateUrl('faq_question_create'),
                'desc' => $this->trans('Add new question', [], 'Modules.Faq.Admin'),
                'icon' => 'add_circle_outline',
            ],
            'generate' => [
                'href' => $this->generateUrl('faq_generate', ['redirect' => 'faq_question_index']),
                'desc' => $this->trans('Generate demo data', [], 'Modules.Faq.Admin'),
                'icon' => 'auto_fix_high',
                'class' => 'btn-outline-secondary',
            ],
        ];
    }
}
