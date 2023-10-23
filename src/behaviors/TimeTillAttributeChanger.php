<?php

declare(strict_types=1);

namespace hipanel\modules\finance\behaviors;

use DateTime;
use hipanel\modules\finance\models\query\BillQuery;
use hipanel\modules\finance\models\query\ChargeQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class TimeTillAttributeChanger extends AttributeBehavior
{
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_INIT => 'fixTimeTill',
        ];
    }

    public function fixTimeTill(): void
    {
        $request = Yii::$app->request;
        $datePicker = $request->get('date-picker');
        if ($datePicker && ($this->owner instanceof BillQuery || $this->owner instanceof ChargeQuery)) {
            $data = $request->get('BillSearch') ?? $request->get('ChargeSearch');
            $this->owner->andWhere(['time_till' => (new DateTime($data['time_till']))->modify("+1 day")->format("Y-m-d")]);
        }
    }
}
