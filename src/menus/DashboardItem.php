<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\menus;

use hipanel\helpers\Url;
use hipanel\modules\client\ClientWithCounters;
use hiqdev\yii2\menus\Menu;
use Yii;

class DashboardItem extends Menu
{
    protected ClientWithCounters $clientWithCounters;

    public function __construct(ClientWithCounters $clientWithCounters, $config = [])
    {
        $this->clientWithCounters = $clientWithCounters;
        parent::__construct($config);
    }

    public function items()
    {
        $items = [];
        if (Yii::$app->user->can('bill.read')) {
            $items['bill'] = [
                'label' => $this->render('dashboardBillItem', $this->clientWithCounters->getWidgetData('bill')),
                'encode' => false,
            ];
        }
        if (Yii::$app->user->can('manage')) {
            $items['tariff'] = [
                'label' => $this->render('dashboardTariffItem', array_merge($this->clientWithCounters->getWidgetData('tariff'), [
                    'route' => Url::toRoute('@plan/index'),
                ])),
                'encode' => false,
            ];
        }

        return $items;
    }
}
