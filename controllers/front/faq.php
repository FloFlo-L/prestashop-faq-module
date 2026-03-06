<?php

declare(strict_types=1);

use Module\Faq\Repository\FaqRepository;

class FaqFaqModuleFrontController extends ModuleFrontController
{
    public function initContent(): void
    {
        $this->registerStylesheet(
            'module-faq-front',
            'modules/faq/views/css/faq-front.css',
            ['media' => 'all', 'priority' => 200]
        );
        $this->registerJavascript(
            'module-faq-accordion',
            'modules/faq/views/js/faq-accordion.js',
            ['position' => 'bottom', 'priority' => 200]
        );
        $this->registerJavascript(
            'module-faq-tabs',
            'modules/faq/views/js/faq-tabs.js',
            ['position' => 'bottom', 'priority' => 201]
        );
        parent::initContent();

        $idLang = (int) $this->context->language->id;

        $this->context->smarty->assign([
            'faq_categories' => FaqRepository::getFrontCategories($idLang),
            'faq_title' => Configuration::get('FAQ_PAGE_TITLE', $idLang),
            'faq_subtitle' => Configuration::get('FAQ_PAGE_SUBTITLE', $idLang),
        ]);
        $this->setTemplate('module:faq/views/templates/front/faq.tpl');
    }
}
