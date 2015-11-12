<?php

/*
 * Payment merchants extension for Yii2
 *
 * @link      https://github.com/hiqdev/yii2-merchant
 * @package   yii2-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
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
