<?php

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\widgets\gridLegend\BaseGridLegend;
use hipanel\widgets\gridLegend\GridLegendInterface;

final class SaleGridLegend extends BaseGridLegend implements GridLegendInterface
{

    /**
     * @return array
     */
    public function items()
    {
        $unsaleTime = $this->model->unsale_time;

        return [
            [
                'label' => ['hipanel:finance:sale', 'Closed sale'],
                'color' => '#f2dede',
                'rule' => !empty($this->model->unsale_time) && !$this->isFuture($unsaleTime),
            ],
            [
                'label' => ['hipanel:finance:sale', 'Upcoming close'],
                'color' => '#fcf8e3',
                'rule' => !empty($this->model->unsale_time) && $this->isFuture($unsaleTime),
            ],
        ];
    }

    private function isFuture(string $date): bool
    {
        return strtotime($date) > time();
    }
}
