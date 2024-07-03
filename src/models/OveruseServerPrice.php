<?php declare(strict_types=1);

namespace hipanel\modules\finance\models;

use hipanel\base\ModelTrait;
use Yii;

class OveruseServerPrice extends ProgressivePrice
{
    use ModelTrait;

    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['count_aggregated_traffic'], 'boolean', 'trueValue' => 1, 'falseValue' => 0];

        return $rules;
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'count_aggregated_traffic' => Yii::t('hipanel.finance.price', 'Calculate aggregated traffic'),
        ]);
    }

    public function isServer95Traf(): bool
    {
        return str_starts_with($this->type, 'overuse,server_traf95_max');
    }

}
