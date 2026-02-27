<?php

declare(strict_types=1);

namespace Module\Faq\Controller\Admin;

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Response;

class FaqQuestionController extends PrestaShopAdminController
{
    public function index(): Response
    {
        return $this->render('@Modules/faq/views/templates/admin/question/index.html.twig');
    }
}
