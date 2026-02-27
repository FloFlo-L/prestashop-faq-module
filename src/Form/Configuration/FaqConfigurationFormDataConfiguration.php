<?php

declare(strict_types=1);

namespace Module\Faq\Form\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

class FaqConfigurationFormDataConfiguration implements DataConfigurationInterface
{
    public const FAQ_PAGE_TITLE = 'FAQ_PAGE_TITLE';
    public const FAQ_PAGE_SUBTITLE = 'FAQ_PAGE_SUBTITLE';

    public function __construct(private readonly ConfigurationInterface $configuration)
    {
    }

    public function getConfiguration(): array
    {
        $title = $this->configuration->get(self::FAQ_PAGE_TITLE);
        $subtitle = $this->configuration->get(self::FAQ_PAGE_SUBTITLE);

        return [
            'title' => is_array($title) ? $title : [],
            'subtitle' => is_array($subtitle) ? $subtitle : [],
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set(self::FAQ_PAGE_TITLE, $configuration['title']);
            $this->configuration->set(self::FAQ_PAGE_SUBTITLE, $configuration['subtitle']);
        }

        return $errors;
    }

    public function validateConfiguration(array $configuration): bool
    {
        return isset($configuration['title'], $configuration['subtitle']);
    }
}
