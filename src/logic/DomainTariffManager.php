<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\DomainTariffForm;
use hipanel\modules\finance\models\Tariff;
use hiqdev\hiart\ErrorResponseException;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class DomainTariffManager extends TariffManager
{
    /**
     * @var DomainTariffForm
     */
    public $model;

    /**
     * @inheritdoc
     */
    protected $type = 'domain';

    public function __construct($model = null)
    {
        if (!Yii::getAlias('@domain', true)) {
            throw new NotFoundHttpException('Domain module is missing');
        }

        parent::__construct($model);
    }

    protected function setModel($model = null)
    {
        $this->model = (new DomainTariffForm())->fill($this->getZones(), $this->baseModel, $model);
    }

    protected function buildData()
    {

    }

    public function insert()
    {
        $data = $this->model->toArray();

        try {
            $result = Tariff::perform('Create', $data);
        } catch (ErrorResponseException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), 0, $e);
        }

        $this->model->id = $result['id'];

        return true;
    }

    public function update()
    {
        $data = $this->model->toArray();

        try {
            $result = Tariff::perform('Update', $data);
        } catch (ErrorResponseException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), 0, $e);
        }

        return true;
    }

    /**
     * @return array
     */
    protected function getZones()
    {
        return Yii::$app->hiart->get('getZones');
    }
}
