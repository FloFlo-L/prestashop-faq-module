<?php

declare(strict_types=1);

use Module\Faq\Database\FaqInstaller;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

class Faq extends Module
{
    /** @var string[] All tab class names used by this module */
    private $tabClassNames = [
        'AdminFaqConfiguration',
        'AdminFaqConfiguration_MTR',
        'AdminFaqCategory',
        'AdminFaqQuestion',
    ];

    public function __construct()
    {
        $this->name = 'faq';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => '9.99.99'];
        $this->author = 'Florian Lescribaa';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('FAQ', [], 'Modules.Faq.Admin');
        $this->description = $this->trans('Create a professional FAQ page for your PrestaShop store.', [], 'Modules.Faq.Admin');

        // Tabs are registered manually in installTabs() to avoid the _MTR duplication mechanism
        $this->tabs = [];
    }

    public function install()
    {
        return
            $this->removeStaleTabs() &&
            $this->installTables() &&
            parent::install() &&
            $this->installTabs()
        ;
    }

    public function uninstall()
    {
        return
            $this->removeStaleTabs() &&
            $this->removeTables() &&
            parent::uninstall()
        ;
    }

    private function installTabs(): bool
    {
        $idImprove = (int) Tab::getIdFromClassName('IMPROVE');
        $languages = Language::getLanguages();

        // Parent tab (group header — like native PS tabs e.g. AdminParentThemes)
        $parent = new Tab();
        $parent->class_name = 'AdminFaqConfiguration';
        $parent->id_parent = $idImprove;
        $parent->active = 1;
        $parent->icon = 'school';
        foreach ($languages as $lang) {
            $parent->name[$lang['id_lang']] = 'FAQ';
        }
        if (!$parent->save()) {
            return false;
        }

        // Categories tab
        $categoryTab = new Tab();
        $categoryTab->class_name = 'AdminFaqCategory';
        $categoryTab->route_name = 'faq_category_index';
        $categoryTab->id_parent = $parent->id;
        $categoryTab->active = 1;
        foreach ($languages as $lang) {
            $categoryTab->name[$lang['id_lang']] = $this->trans('Categories', [], 'Modules.Faq.Admin', $lang['locale']);
        }
        if (!$categoryTab->save()) {
            return false;
        }

        // Questions / Answers tab
        $questionTab = new Tab();
        $questionTab->class_name = 'AdminFaqQuestion';
        $questionTab->route_name = 'faq_question_index';
        $questionTab->id_parent = $parent->id;
        $questionTab->active = 1;
        foreach ($languages as $lang) {
            $questionTab->name[$lang['id_lang']] = $this->trans('Questions / Answers', [], 'Modules.Faq.Admin', $lang['locale']);
        }

        return $questionTab->save();
    }

    private function removeStaleTabs(): bool
    {
        foreach ($this->tabClassNames as $className) {
            $id = Tab::getIdFromClassName($className);
            if ($id) {
                $tab = new Tab($id);
                $tab->delete();
            }
        }

        return true;
    }

    public function getContent(): void
    {
        $route = $this->get('router')->generate('faq_configuration');
        Tools::redirectAdmin($route);
    }

    private function installTables(): bool
    {
        /** @var FaqInstaller $installer */
        $installer = $this->getInstaller();
        $errors = $installer->createTables();

        return empty($errors);
    }

    private function removeTables(): bool
    {
        /** @var FaqInstaller $installer */
        $installer = $this->getInstaller();
        $errors = $installer->dropTables();

        return empty($errors);
    }

    private function getInstaller(): FaqInstaller
    {
        try {
            $installer = $this->get(FaqInstaller::class);
        } catch (Exception) {
            $installer = null;
        }

        if (!$installer) {
            $installer = new FaqInstaller(
                $this->get('doctrine.dbal.default_connection'),
                $this->getContainer()->getParameter('database_prefix')
            );
        }

        return $installer;
    }
}
