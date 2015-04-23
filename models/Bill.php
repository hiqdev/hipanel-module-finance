<?php
/**
 * @link    http://hiqdev.com/hipanel-module-finance
 * @license http://hiqdev.com/hipanel-module-finance/license
 * @copyright Copyright (c) 2015 HiQDev
 */

namespace hipanel\modules\finance\models;

use Yii;

class Bill extends \hipanel\base\Model
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
            [[ 'time' ],                            'date' ],
            [[ 'sum', 'balance' ],                  'number' ],
            [[ 'currency' ],                        'safe' ],
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
