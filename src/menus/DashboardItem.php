<?php
/**
 * Dashboard Plugin for HiPanel.
 *
 * @link      https://github.com/hiqdev/hipanel-module-dashboard
 * @package   hipanel-module-dashboard
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\menus;

use hipanel\modules\dashboard\DashboardInterface;
use Yii;

class DashboardItem extends \hiqdev\yii2\menus\Menu
{
    protected $dashboard;

    public function __construct(DashboardInterface $dashboard, $config = [])
    {
        $this->dashboard = $dashboard;
        parent::__construct($config);
    }

    public function items()
    {
        return [
            'bill' => [
                'label' => $this->render('dashboardBillItem', $this->dashboard->mget(['model'])),
                'encode' => false,
                'visible' => Yii::$app->user->can('bill.read'),
            ],
            'tariff' => [
                'label' => $this->render('dashboardTariffItem'),
                'encode' => false,
                'visible' => Yii::$app->user->can('manage'),
            ],
        ];
    }
}
