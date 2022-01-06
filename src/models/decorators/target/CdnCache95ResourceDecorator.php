<?php declare(strict_types=1);

namespace hipanel\modules\finance\models\decorators\target;

use Yii;

class CdnCache95ResourceDecorator extends CdnCacheResourceDecorator
{
    public function displayTitle()
    {
        return Yii::t('hipanel.finance.resource', 'CDN Cache 95 percentile');
    }
}
