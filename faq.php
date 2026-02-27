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
    /** @var array List of hooks used by the module*/
    private $hooks = [];

    public function __construct()
    {
        $this->name = 'faq';
        $this->version = '1.0.0';
        $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => '9.99.99'];
        $this->author = 'Florian Lescribaa';
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('FAQ');
        $this->description = $this->l('Create a professional FAQ page for your PrestaShop store.');
    }

    /**
     * Install the module, add tables and register hooks
     */
    public function install()
    {
        return 
            $this->installTables() && 
            parent::install() &&
            $this->registerHook($this->hooks)
        ;
    }

    /**
     * Uninstall the module, remove tables and unregister hooks
     */
    public function uninstall()
    {
        return 
            $this->removeTables() && 
            parent::uninstall() && 
            $this->unregisterHook($this->hooks)
        ;
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
            // Catch exception in case container is not available, or service is not available
            $installer = null;
        }

        // During install process the modules's service is not available yet, so we build it manually
        if (!$installer) {
            $installer = new FaqInstaller(
                $this->get('doctrine.dbal.default_connection'),
                $this->getContainer()->getParameter('database_prefix')
            );
        }

        return $installer;
    }
}