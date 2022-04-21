<?php
declare(strict_types=1);

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;

class PlanType extends Model
{
    use ModelTrait;

    public static function tableName()
    {
        return 'plan';
    }

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'label'], 'string'],
        ];
    }
}
