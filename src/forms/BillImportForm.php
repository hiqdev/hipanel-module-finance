<?php

namespace hipanel\modules\finance\forms;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\models\Bill;
use yii\base\InvalidValueException;
use yii\helpers\ArrayHelper;

class BillImportForm extends \yii\base\Model
{
    protected $_types;

    public $data;

    public function attributes()
    {
        return ['data'];
    }

    public function rules()
    {
        return [
            ['data', 'safe'],
        ];
    }

    public function parse()
    {
        $bills = [];
        $billTemplate = new Bill(['scenario' => Bill::SCENARIO_CREATE]);

        $lines = explode("\n", $this->data);
        foreach ($lines as $line) {
            $bills[] = $bill = clone $billTemplate;

            $chunks = explode(';', $line);
            if (count($chunks) !== 6) {
                throw new InvalidValueException('Line "' . $line . '" is malformed"');
            }

            list($client, $time, $sum, $currency, $type, $label) = array_map('trim', $chunks);
            $bill->setAttributes(compact('client', 'time', 'sum', 'currency', 'type', 'label'));
        }

        $this->resolveClients(ArrayHelper::getColumn($bills, 'client'));

        foreach ($bills as $bill) {
            $bill->time = \Yii::$app->formatter->asDatetime($this->resolveTime($bill->time), 'php:d.m.Y H:i:s');
            $bill->type = $this->resolveType($bill->type);
            $bill->client_id = $this->convertClientToId($bill->client);
        }

        return empty($bills) ? false : $bills;
    }

    protected function resolveTime($time)
    {
        $timestamp = strtotime($time);

        if ($timestamp !== false) {
            return $timestamp;
        }

        if ($time === 'this' || $time === 'thisMonth') {
            return strtotime('first day of this month midnight');
        }

        if ($time === 'prev' || $time === 'prevMonth') {
            return strtotime('first day of last month midnight');
        }

        return time();
    }

    protected function resolveType($type)
    {
        $types = $this->getTypes();

        // Type is a normal key
        if (isset($types[$type])) {
            return $type;
        }

        // Type is a title of type instead of its key
        if (in_array($type, $types)) {
            return array_search($type, $types);
        }

        // Assuming only second part is passed. Match from the end
        $foundKey = null;
        foreach ($types as $key => $title) {
            list(, $name) = explode(',', $key);
            if ($name === $type) {
                if ($foundKey !== null) {
                    throw new InvalidValueException('Payment type "' . $type . '" is ambiguous');
                }

                $foundKey = $key;
            }
        }

        if ($foundKey) {
            return $foundKey;
        }

        throw new InvalidValueException('Payment type "' . $type . '" is not recognized');
    }

    protected function convertClientToId($client)
    {
        if (!isset($this->clientsMap[$client])) {
            throw new InvalidValueException('Client "' . $client . '" was not found');
        }

        return $this->clientsMap[$client];
    }

    public function setTypes($types)
    {
        $result = [];

        foreach ($types as $category) {
            foreach ($category as $key => $title) {
                $result[$key] = $title;
            }
        }

        $this->_types = $result;
    }

    private $clientsMap = [];

    private function resolveClients($logins)
    {
        $clients = Client::find()->where(['login' => $logins])->all();
        $this->clientsMap = array_combine(ArrayHelper::getColumn($clients, 'login'),
            ArrayHelper::getColumn($clients, 'id'));
    }

    public function getTypes()
    {
        return $this->_types;
    }
}
