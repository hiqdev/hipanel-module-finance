<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\merchant;

use Yii;

class Deposit extends \yii\base\Model
{
    public $sum;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sum'], 'number'],
            [['sum'], 'required'],
            [['sum'], 'compare', 'operator' => '>', 'compareValue' => 0],
        ];
    }

    public function attributes()
    {
        return ['sum'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sum' => Yii::t('app', 'Sum'),
        ];
    }
}
