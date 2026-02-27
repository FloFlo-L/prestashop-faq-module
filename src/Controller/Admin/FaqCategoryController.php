<?php

declare(strict_types=1);

namespace Module\Faq\Controller\Admin;

use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use Symfony\Component\HttpFoundation\Response;

class FaqCategoryController extends PrestaShopAdminController
{
    public function index(): Response
    {
        return $this->render('@Modules/faq/views/templates/admin/category/index.html.twig');
    }
}
