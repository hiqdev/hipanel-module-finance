<?php

namespace hipanel\modules\finance\models;

use Yii;

/**
 * Class ServerResourceTypesProvider
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class ServerResourceTypesProvider implements ServerResourceTypesProviderInterface
{
    public function getTypes()
    {
        return [
            ServerResource::TYPE_MONTHLY => Yii::t('hipanel:finance:tariff', 'Monthly fee'),
            ServerResource::TYPE_ISP5 => Yii::t('hipanel:finance:tariff', 'ISP Manager 5'),
            ServerResource::TYPE_ISP => Yii::t('hipanel:finance:tariff', 'ISP Manager'),
            ServerResource::TYPE_SUPPORT_TIME => Yii::t('hipanel:finance:tariff', 'Support time'),
            ServerResource::TYPE_IP_NUMBER => Yii::t('hipanel:finance:tariff', 'IP addresses count'),
            ServerResource::TYPE_SERVER_TRAF_MAX => Yii::t('hipanel:finance:tariff', 'Server traffic'),
            ServerResource::TYPE_SERVER_TRAF95_MAX => Yii::t('hipanel:finance:tariff', '95 percentile traffic'),
            ServerResource::TYPE_BACKUP_DU => Yii::t('hipanel:finance:tariff', 'Backup disk usage'),
        ];
    }
}
