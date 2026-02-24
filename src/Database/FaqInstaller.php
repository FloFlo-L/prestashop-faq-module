<?php

declare(strict_types=1);

namespace Module\Faq\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DBALException;

class FaqInstaller
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix
    ) {
    }

    /**
     * Create tables for the module
     *
     * @return array An array of errors, empty if no error occurred
     */
    public function createTables(): array
    {
        $errors = [];
        $this->dropTables();
        $sqlInstallFile = __DIR__ . '/../../Resources/data/install.sql';
        $sqlContent = file_get_contents($sqlInstallFile);
        $sqlContent = str_replace('PREFIX_', $this->dbPrefix, $sqlContent);
        $sqlQueries = array_filter(array_map('trim', explode(';', $sqlContent)));

        foreach ($sqlQueries as $query) {

            try {
                $this->connection->executeQuery($query);
            } catch (DBALException $e) {
                $errors[] = [
                    'key' => $e->getMessage(),
                    'parameters' => [],
                    'domain' => 'Admin.Modules.Notification',
                ];
            }
        }

        return $errors;
    }

    /**
     * Drop tables for the module
     *
     * @return array An array of errors, empty if no error occurred
     */
    public function dropTables(): array
    {
        $errors = [];
        $tableNames = [
            'faq_lang',
            'faq',
            'faq_category_lang',
            'faq_category',
        ];
        foreach ($tableNames as $tableName) {
            $sql = 'DROP TABLE IF EXISTS ' . $this->dbPrefix . $tableName;
            try {
                $this->connection->executeQuery($sql);
            } catch (DBALException $e) {
                $errors[] = [
                    'key' => $e->getMessage(),
                    'parameters' => [],
                    'domain' => 'Admin.Modules.Notification',
                ];
            }
        }

        return $errors;
    }
}