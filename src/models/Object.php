<?php

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;

/**
 * Class Object
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $class_name
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class Object extends Model
{
    use ModelTrait;

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name', 'class_name', 'type'], 'safe']
        ];
    }
}
