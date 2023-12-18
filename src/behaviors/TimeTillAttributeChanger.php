<?php

declare(strict_types=1);

namespace hipanel\modules\finance\behaviors;

use DateTime;
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
        if ($datePicker && $params = $this->getSearchParams($request->get())) {
            $this->owner->andWhere(['time_till' => (new DateTime($params['time_till']))->modify("+1 day")->format("Y-m-d")]);
        }
    }

    private function getSearchParams(mixed $get): ?array
    {
        return $get[current(preg_grep('/Search$/', array_keys($get)))];
    }
}
