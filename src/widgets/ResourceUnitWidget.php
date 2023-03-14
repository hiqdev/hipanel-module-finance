<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Widget;

final class ResourceUnitWidget extends Widget
{
    /**
     * @var ActiveField
     */
    public $activeField;

    /** @var ServerResource */
    public $resource;

    public function run()
    {
        echo $this->activeField->dropDownList($this->getUnitOptions());
    }

    protected function getUnitOptions()
    {
        $unitGroup = [
            'speed' => [
                'bps' => Yii::t('hipanel.finance.units', 'bps'),
                'kbps' => Yii::t('hipanel.finance.units', 'kbps'),
                'mbps' => Yii::t('hipanel.finance.units', 'Mbps'),
                'gbps' => Yii::t('hipanel.finance.units', 'Gbps'),
                'tbps' => Yii::t('hipanel.finance.units', 'Tbps'),
            ],
            'size' => [
                'mb' => Yii::t('hipanel.finance.units', 'MB'),
                'gb' => Yii::t('hipanel.finance.units', 'GB'),
                'tb' => Yii::t('hipanel.finance.units', 'TB'),
            ],
            'time' => [
                'hour' => Yii::t('hipanel.finance.units', 'Hours'),
                'min' => Yii::t('hipanel.finance.units', 'Minutes'),
            ],
            'items' => [
                'items' => Yii::t('hipanel.finance.units', 'Items'),
                'units' => Yii::t('hipanel.finance.units', 'Units'),
                'files' => Yii::t('hipanel.finance.units', 'Files'),
            ],
            'power' => [
                'w' => Yii::t('hipanel.finance.units', 'W'),
                'kW' => Yii::t('hipanel.finance.units', 'kW'),
            ]
        ];

        $resource = [
            'monthly' => $unitGroup['items'],
            'support_time' => $unitGroup['time'],
            'ip_num' => $unitGroup['items'],
            'mail_num' => $unitGroup['items'],
            'ssd_files' => $unitGroup['items'],
            'sata_files' => $unitGroup['items'],
            'domain_num' => $unitGroup['items'],
            'db_num' => $unitGroup['items'],
            'server_traf_max' => $unitGroup['size'],
            'backup_du' => $unitGroup['size'],
            'server_du' => $unitGroup['size'],
            'mail_du' => $unitGroup['size'],
            'account_du' => $unitGroup['size'],
            'backup_traf' => $unitGroup['size'],
            'domain_traf' => $unitGroup['size'],
            'ip_traf_max' => $unitGroup['size'],
            'account_traf' => $unitGroup['size'],
            'server_traf95_max' => $unitGroup['speed'],
            'power' => $unitGroup['power'],
        ];

        return isset($resource[$this->resource->type]) ? $resource[$this->resource->type] : [];
    }
}
