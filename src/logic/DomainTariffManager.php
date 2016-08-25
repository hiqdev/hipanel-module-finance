<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\DomainTariffForm;
use hipanel\modules\finance\models\Tariff;
use hiqdev\hiart\ErrorResponseException;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class DomainTariffManager extends AbstractTariffManager
{
    /**
     * @var DomainTariffForm
     */
    public $form;

    /**
     * @inheritdoc
     */
    protected $type = 'domain';

    public function init()
    {
        parent::init();

        if (!Yii::getAlias('@domain', true)) {
            throw new NotFoundHttpException('Domain module is missing');
        }
    }

    protected function createForm($tariff = null)
    {
        $this->form = (new DomainTariffForm(['scenario' => $this->scenario]))
            ->fill($this->getZones(), $this->baseTariff, $tariff);
    }

    public function insert()
    {
        $data = $this->form->toArray();

        try {
            $result = Tariff::perform('Create', $data);
        } catch (ErrorResponseException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), 0, $e);
        }

        $this->form->id = $result['id'];

        return true;
    }

    public function update()
    {
        $data = $this->form->toArray();

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
