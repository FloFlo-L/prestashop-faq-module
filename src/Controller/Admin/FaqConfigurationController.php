<?php

declare(strict_types=1);

namespace Module\Faq\Controller\Admin;

use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FaqConfigurationController extends PrestaShopAdminController
{
    public function index(
        Request $request,
        #[Autowire(service: 'module.faq.form.configuration_form_data_handler')]
        FormHandlerInterface $formHandler,
    ): Response {
        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $errors = $formHandler->save($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', [], 'Admin.Notifications.Success'));

                return $this->redirectToRoute('faq_configuration');
            }

            $this->addFlashErrors($errors);
        }

        return $this->render('@Modules/faq/views/templates/admin/configuration.html.twig', [
            'faqConfigurationForm' => $form->createView(),
        ]);
    }
}
