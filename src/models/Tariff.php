<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (https://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

class Tariff extends \hipanel\base\Model
{
    use \hipanel\base\ModelTrait;

    /**
     * @var array resources. Filled on search query with option `with_resources`
     */
    public $resources = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['client_id', 'seller_id', 'id'],      'integer'],
            [['client', 'seller', 'bill', 'name'],  'safe'],
            [['domain', 'server'],                  'safe'],
            [['tariff'],                            'safe'],
            [['type_id', 'state_id'],               'integer'],
            [['type', 'state'],                     'safe'],
            [['used'],                              'integer'],
            [['note'],                              'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
        ]);
    }
}
