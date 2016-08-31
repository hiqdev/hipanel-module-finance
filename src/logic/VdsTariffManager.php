<?php

namespace hipanel\modules\finance\logic;

use hipanel\modules\finance\forms\DomainTariffForm;
use hipanel\modules\finance\forms\VdsTariffForm;
use hipanel\modules\finance\models\Tariff;
use hiqdev\hiart\ErrorResponseException;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class VdsTariffManager extends AbstractTariffManager
{
    /**
     * @var VdsTariffForm
     */
    public $form;

    /**
     * @inheritdoc
     */
    protected $type = 'server';

    public function init()
    {
        parent::init();

        if (!Yii::getAlias('@server', true)) {
            throw new NotFoundHttpException('Domain module is missing');
        }
    }

    protected function buildForm()
    {
        $this->form = new VdsTariffForm([
            'scenario' => $this->scenario,
            'baseTariffs' => $this->baseTariffs,
            'tariff' => $this->tariff
        ]);
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
}
