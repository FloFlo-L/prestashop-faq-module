<?php

declare(strict_types=1);

namespace Module\Faq\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;

class FaqCategoryFilters extends Filters
{
    public static function getDefaults(): array
    {
        return [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'position',
            'sortOrder' => 'asc',
            'filters' => [],
        ];
    }
}
