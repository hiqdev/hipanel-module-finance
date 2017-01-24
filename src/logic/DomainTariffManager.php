<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

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
     * {@inheritdoc}
     */
    protected $type = Tariff::TYPE_DOMAIN;

    public function init()
    {
        parent::init();

        if (!Yii::getAlias('@domain', true)) {
            throw new NotFoundHttpException('Domain module is missing');
        }

        $this->formOptions['zones'] = $this->getZones();
    }

    public function insert()
    {
        $data = $this->form->toArray();

        try {
            $result = Tariff::perform('create', $data);
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
            $result = Tariff::perform('update', $data);
        } catch (ErrorResponseException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), 0, $e);
        }

        return true;
    }

    protected function getFormOptions()
    {
        return array_merge([
            'class' => DomainTariffForm::class,
            'zones' => $this->getZones(),
        ], parent::getFormOptions());
    }

    /**
     * @return array
     */
    protected function getZones()
    {
        return Yii::$app->hiart->get('getZones');
    }
}
