<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use Yii;

class Change extends \hipanel\base\Model
{
    const STATE_NEW = 'new';
    const STATE_APPROVED = 'approved';
    const STATE_REJECTED = 'rejected';

    use \hipanel\base\ModelTrait;

    public static $i18nDictionary = 'hipanel/finance/change';

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'seller_id', 'client_id'], 'integer'],
            [['client', 'seller', 'type'], 'safe'],
            [['state', 'class', 'time'], 'safe'],
            [['client', 'seller', 'type'], 'safe'],
            [['user_comment', 'tech_comment', 'finish_time'], 'safe'],
            [['params'], 'safe'],
            [['id', 'comment'], 'required', 'on' => ['approve', 'reject']],
        ];
    }

    public static function find()
    {
        $query = parent::find();
        $query->andWhere(['with_params' => 1]);
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'client' => Yii::t('hipanel', 'Client'),
            'comment' => Yii::t('hipanel', 'Comment'),
            'client_id' => Yii::t('hipanel', 'Client'),
            'time' => Yii::t('hipanel', 'Time'),
            'state' => Yii::t('hipanel', 'State'),
        ]);
    }
}
