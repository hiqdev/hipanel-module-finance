<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;

/**
 * Class Object.
 *
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $label
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TargetObject extends Model
{
    use ModelTrait;

    public function rules()
    {
        return [
            [['id', 'no'], 'integer'],
            [['name', 'label', 'type', 'model_type'], 'safe'],
        ];
    }
}
