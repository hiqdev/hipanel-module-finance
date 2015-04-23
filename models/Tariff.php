<?php
/**
 * @link    http://hiqdev.com/hipanel-module-finance
 * @license http://hiqdev.com/hipanel-module-finance/license
 * @copyright Copyright (c) 2015 HiQDev
 */

namespace hipanel\modules\finance\models;

use Yii;

class Tariff extends \hipanel\base\Model
{

    use \hipanel\base\ModelTrait;

    /**
     * @inheritdoc
     */
    public function rules ()
    {
        return [
            [[ 'client_id', 'seller_id', 'id' ],    'integer' ],
            [[ 'client', 'seller', 'bill' ],        'safe' ],
            [[ 'domain', 'server'       ],          'safe' ],
            [[ 'tariff' ],                          'safe' ],
            [[ 'type_id', 'state_id' ],             'integer' ],
            [[ 'type', 'state' ],                   'safe' ],
            [[ 'used' ],                            'integer' ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels ()
    {
        return $this->mergeAttributeLabels([
        ]);
    }
}
