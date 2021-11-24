<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators\target;

use hipanel\modules\finance\models\decorators\server\BackupResourceDecorator;
use Yii;

class SnapshotDuResourceDecorator extends BackupResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel.finance.resource', 'Snapshot disk usage');
    }
}
