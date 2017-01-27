<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use Yii;

/**
 * Class RUse. Used to represent Resources Usage.
 */
class RUse extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    public function formName()
    {
        return 'Use';
    }

    public static function form()
    {
        return 'use';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'last', 'total'], 'integer'],
            [['date', 'type', 'aggregation'], 'safe'],
        ];
    }

    public function getDisplayDate()
    {
        if ($this->aggregation === 'month') {
            return Yii::$app->formatter->asDate(strtotime($this->date), 'LLL y');
        } elseif ($this->aggregation === 'week') {
            return Yii::$app->formatter->asDate(strtotime($this->date), 'dd LLL y');
        } elseif ($this->aggregation === 'day') {
            return Yii::$app->formatter->asDate(strtotime($this->date), 'dd LLL y');
        }

        return Yii::$app->formatter->asDate(strtotime($this->date));
    }
}
