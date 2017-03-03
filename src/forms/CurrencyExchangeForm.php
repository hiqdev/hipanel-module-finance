<?php

namespace hipanel\modules\finance\forms;

use hipanel\base\Model;
use hipanel\modules\finance\models\Bill;
use hiqdev\hiart\ResponseErrorException;

class CurrencyExchangeForm extends Model
{
    /**
     * @var int
     */
    public $client_id;

    /**
     * @var string source currency
     */
    public $from;

    /**
     * @var string target currency
     */
    public $to;

    /**
     * @var float original sum to be exchanged
     */
    public $sum;

    public $result;

    public function rules()
    {
        return [
            [['client_id', 'from', 'to', 'sum'], 'required'],
            [['sum'], 'double'],
            [['from', 'to'], 'string', 'length' => 3]
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        try {
            $result = Bill::perform('create-exchange', $this->getAttributes());
        } catch (ResponseErrorException $e) {
            $this->addError('client_id', $e->getMessage());
            return false;
        }

        return $result['id'];
    }
}
