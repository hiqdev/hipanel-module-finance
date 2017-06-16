<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\forms;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\models\Bill;
use Yii;
use yii\base\InvalidValueException;
use yii\helpers\ArrayHelper;

/**
 * Class BillImportForm provides functionality to parse CSV data.
 *
 * Usage:
 *
 * ```php
 * $model = new BillImportForm([
 *     'billTypes' => [
 *         'deposit,webmoney' => 'WebMoney account deposit'
 *     ]
 * ]);
 *
 * $model->data = 'silverfire;now;10;usd;webmoney;test';
 * $models = $model->parse();
 *
 * foreach ($models as $model) {
 *     $model->save();
 * }
 * ```
 */
class BillImportForm extends \yii\base\Model
{
    /**
     * @var string
     */
    public $data;
    /**
     * @var array Array of possible bill types.
     * Key - full bill type
     * Value - bill type title
     */
    public $billTypes = [];
    /**
     * @var array map to find client id by login.
     * Key - login
     * Value - id
     */
    private $clientsMap = [];

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return ['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'data' => Yii::t('hipanel:finance', 'Rows for import'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['data', 'safe'],
        ];
    }

    /**
     * Parses [[data]] attribute and creates [[Bill]] model from each line.
     *
     * @return Bill[]|false Array of [[Bill]] models on success or `false` on parsing error
     */
    public function parse()
    {
        $bills = [];
        $billTemplate = new Bill(['scenario' => Bill::SCENARIO_CREATE]);

        $lines = explode("\n", $this->data);

        try {
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }

                $bills[] = $bill = clone $billTemplate;

                list($client, $time, $sum, $currency, $type, $label) = $this->splitLine($line);
                $bill->setAttributes(compact('client', 'time', 'sum', 'currency', 'type', 'label'));
            }

            $this->resolveClients(ArrayHelper::getColumn($bills, 'client'));

            foreach ($bills as $bill) {
                $bill->time = \Yii::$app->formatter->asDatetime($this->resolveTime($bill->time), 'php:d.m.Y H:i:s');
                $bill->type = $this->resolveType($bill->type);
                $bill->client_id = $this->convertClientToId($bill->client);
            }
        } catch (InvalidValueException $e) {
            $this->addError('data', $e->getMessage());

            return false;
        }

        return empty($bills) ? false : $bills;
    }

    /**
     * Splits $line for chunks by `;` character.
     * Ensures there are exactly 6 chunks.
     * Trims each value before return.
     *
     * @param string $line to be exploded
     * @return array
     */
    protected function splitLine($line)
    {
        $chunks = explode(';', $line);
        if (count($chunks) !== 6) {
            throw new InvalidValueException('Line "' . $line . '" is malformed');
        }

        return array_map('trim', $chunks);
    }

    /**
     * @param array $logins all logins used current import session to be pre-fetched
     * @void
     */
    private function resolveClients($logins)
    {
        $clients = Client::find()->where(['logins' => $logins])->all();
        $this->clientsMap = array_combine(ArrayHelper::getColumn($clients, 'login'),
            ArrayHelper::getColumn($clients, 'id'));
    }

    /**
     * Resolves $time to a UNIX epoch timestamp.
     *
     * @param $time
     * @return int UNIX epoch timestamp
     */
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

    /**
     * Resolves payment $type to a normal form.
     *
     * @param string $type
     * @throws InvalidValueException
     * @return string
     */
    protected function resolveType($type)
    {
        $types = $this->billTypes;

        // Type is a normal key
        if (isset($types[$type])) {
            return $type;
        }

        // Type is a title of type instead of its key
        if (in_array($type, $types, true)) {
            return array_search($type, $types, true);
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

    /**
     * Converts client login to ID.
     * Note: [[resolveClients]]] must be called before calling this method.
     *
     * @param string $client
     * @return string|int
     * @see clientMap
     */
    protected function convertClientToId($client)
    {
        if (!isset($this->clientsMap[$client])) {
            throw new InvalidValueException('Client "' . $client . '" was not found');
        }

        return $this->clientsMap[$client];
    }
}
