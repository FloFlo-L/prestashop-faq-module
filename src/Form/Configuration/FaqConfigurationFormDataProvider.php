<?php

declare(strict_types=1);

namespace Module\Faq\Form\Configuration;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

class FaqConfigurationFormDataProvider implements FormDataProviderInterface
{
    public function __construct(
        private readonly FaqConfigurationFormDataConfiguration $dataConfiguration
    ) {
    }

    public function getData(): array
    {
        return $this->dataConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->dataConfiguration->updateConfiguration($data);
    }
}
