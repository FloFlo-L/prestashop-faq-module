<?php

declare(strict_types=1);

namespace Module\Faq\Grid\Filters;

use PrestaShop\PrestaShop\Core\Search\Filters;

class FaqFilters extends Filters
{
    /** @var string */
    protected $filterId = 'faq';

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
