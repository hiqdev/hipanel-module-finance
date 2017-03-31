<?php

namespace hipanel\modules\finance\transaction;

use hiqdev\hiart\AbstractConnection;
use hiqdev\hiart\ConnectionInterface;
use hiqdev\hiart\ResponseErrorException;
use hiqdev\yii2\merchant\transactions\Transaction;
use hiqdev\yii2\merchant\transactions\TransactionException;
use hiqdev\yii2\merchant\transactions\TransactionRepositoryInterface;
use ReflectionObject;
use yii\helpers\Json;

class ApiTransactionRepository implements TransactionRepositoryInterface
{
    /**
     * @var ConnectionInterface|AbstractConnection
     */
    private $connection;

    /**
     * ApiTransactionRepository constructor.
     * @param ConnectionInterface $connection
     */
    function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $id
     * @return Transaction
     * @throws TransactionException when transaction with the specified ID
     * does not exists
     */
    public function findById($id)
    {
        try {
            $data = $this->connection->callWithDisabledAuth(function () use ($id) {
                return $this->connection->createCommand()->perform('merchantTransactionGet', null, ['id' => $id]);
            });
        } catch (ResponseErrorException $e) {
            throw new TransactionException('Failed to get transaction information');
        }

        if (empty($data)) {
            throw new TransactionException('Transaction not found');
        }

        return $this->instantiate($data);
    }

    /**
     * @inheritdoc
     */
    public function insert($transaction)
    {
        return $this->save($transaction);
    }

    /**
     * @inheritdoc
     */
    public function create($id, $merchant, $parameters)
    {
        $transaction = new Transaction($id, $merchant);
        $transaction->setParameters($parameters);

        return $transaction;
    }

    /**
     * @inheritdoc
     */
    public function save($transaction)
    {
        try {
            $data = $transaction->toArray();
            $data['parameters'] = Json::encode($data['parameters']);

            $this->connection->callWithDisabledAuth(function () use ($data) {
                return $this->connection->createCommand()->perform('merchantTransactionSet', null, $data);
            });
        } catch (ResponseErrorException $e) {
            throw new TransactionException('Failed to save transaction');
        }

        return $transaction;
    }

    /**
     * @param $data
     * @return Transaction
     */
    protected function instantiate($data)
    {
        $transaction = $this->create($data['id'], $data['merchant'], $data['parameters']);

        if ($data['success'] !== null) {
            $successReflection = (new ReflectionObject($transaction))->getProperty('success');
            $successReflection->setAccessible(true);
            $successReflection->setValue($transaction, $data['success']);
            $successReflection->setAccessible(false);
        }

        return $transaction;
    }
}
