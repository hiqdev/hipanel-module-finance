<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2021, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\forms;

use hipanel\modules\client\models\Client;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Requisite;
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

    const BILL_FIELD_COUNT_WITHOUT_REQUISITE = 6;
    const BILL_FIELD_COUNT_WITH_REQUISITE = 7;
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
     * @var array map to find client seller by login.
     * Key - login
     * Value - seller
     */
    private $sellerMap = [];

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
    public function parse(): ?array
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

                $bill = clone $billTemplate;
                $splitted = $this->splitLine($line);
                if (count($splitted) === self::BILL_FIELD_COUNT_WITHOUT_REQUISITE) {
                    list($client, $time, $sum, $currency, $type, $label) = $splitted;
                    $requisite = null;
                } else {
                    list($client, $time, $sum, $currency, $type, $label, $requisite) = $splitted;
                }
                $bill->setAttributes(compact('client', 'time', 'sum', 'currency', 'type', 'label', 'quantity', 'requisite'));
                $bills[] = $bill;
                $quantity = 1;
                $bill->populateRelation('charges', []);
            }

            $this->resolveClients(ArrayHelper::getColumn($bills, 'client'));

            foreach ($bills as $bill) {
                $time = $this->resolveTime($bill->time);
                $bill->time = $time !== false ? \Yii::$app->formatter->asDatetime($time, 'php:d.m.Y H:i:s') : false;
                $bill->type = $this->resolveType($bill->type);
                $bill->client_id = $this->convertClientToId($bill->client);
                if ($bill->requisite !== null) {
                    $bill->requisite_id = $this->convertRequisiteNameToId($bill->requisite, $bill->client);
                    if (empty($bill->requisite_id)) {
                        $bill->addError('requisite_id', Yii::t('hipanel:finance', 'Requisite is not found'));
                    }
                }
            }
        } catch (InvalidValueException $e) {
            $this->addError('data', $e->getMessage());

            return null;
        }

        return empty($bills) ? null : $bills;
    }

    /**
     * Splits $line for chunks by `;` character.
     * Ensures there are exactly 6 chunks.
     * Trims each value before return.
     *
     * @param string $line to be exploded
     * @return array
     * @throw InvalidValueException
     */
    private function splitLine(string $line): array
    {
        $chunks = array_map('trim', explode(';', $line));
        if (in_array(count($chunks), [self::BILL_FIELD_COUNT_WITHOUT_REQUISITE, self::BILL_FIELD_COUNT_WITH_REQUISITE], true)) {
            return $chunks;
        }

        throw new InvalidValueException('Line "' . $line . '" is malformed');
    }

    /**
     * @param array $logins all logins used current import session to be pre-fetched
     * @void
     */
    private function resolveClients($logins): void
    {

        $clients = $this->getClients($logins);
        $this->clientsMap = array_combine(ArrayHelper::getColumn($clients, 'login'),
            ArrayHelper::getColumn($clients, 'id'));
        $this->sellerMap = array_combine(ArrayHelper::getColumn($clients, 'login'),
            ArrayHelper::getColumn($clients, 'seller'));
    }

    /**
     * Resolves $time to a UNIX epoch timestamp.
     *
     * @param $time
     * @return int UNIX epoch timestamp
     */
    private function resolveTime(string $time): int
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

        return false;
    }

    /**
     * Resolves payment $type to a normal form.
     *
     * @param string $type
     * @throws InvalidValueException
     * @return string
     */
    private function resolveType(string $type): string
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
    private function convertClientToId(string $client): string
    {
        if (!isset($this->clientsMap[$client])) {
            throw new InvalidValueException('Client "' . $client . '" was not found');
        }

        return $this->clientsMap[$client];
    }

    private function getClients(array $logins): array
    {
        return Yii::$app->cache->getOrSet([__CLASS__, __METHOD__ , $logins], function () use ($logins) {
            return Client::find()
                ->where([
                    'logins' => $logins,
                ])
                ->limit(-1)
                ->all();
        }, 3600);
    }

    private function convertRequisiteNameToId(string $requisite, string $client): ?int
    {
        if (!isset($this->sellerMap[$client])) {
            return null;
        }

        $requisites = $this->getRequisites($this->sellerMap[$client]);

        return isset($requisites[$requisite]) && !empty($requisites[$requisite]) ? (int) $requisites[$requisite] : null;

    }

    private function getRequisites(string $seller): array
    {
        return Yii::$app->cache->getOrSet([__CLASS__, __METHOD__ , $seller], function() use ($seller) {
            $requisites = Requisite::find()
                ->where(['client' => $seller])
                ->limit(-1)
                ->all();
            if (empty($requisites)) {
                return [];
            }

            foreach ($requisites as $requisite) {
                $name2id[$requisite->name] = (int) $requisite->id;
            }

            return $name2id;
        }, 3600);
    }
}
