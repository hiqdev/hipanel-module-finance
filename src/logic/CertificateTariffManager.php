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

use hipanel\models\Ref;
use hipanel\modules\finance\forms\CertificateTariffForm;
use hipanel\modules\finance\forms\DomainTariffForm;
use hipanel\modules\finance\models\Tariff;
use hiqdev\hiart\ConnectionInterface;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class CertificateTariffManager
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CertificateTariffManager extends AbstractTariffManager
{
    /**
     * @var DomainTariffForm
     */
    public $form;

    /**
     * {@inheritdoc}
     */
    protected $type = Tariff::TYPE_CERT;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function __construct(ConnectionInterface $connection, $config = [])
    {
        $this->connection = $connection;

        parent::__construct($config);
    }

    public function init()
    {
        if (!Yii::getAlias('@certificate', true)) {
            throw new InvalidConfigException('Certificate module is missing');
        }

        parent::init();
    }

    protected function determineParentTariff()
    {
        $id = parent::determineParentTariff();

        if ($id === null) {
            $tariff = reset(Tariff::find()
                ->action('get-available-info')
                ->andFilterWhere(['type' => $this->type])
                ->all());

            if ($tariff instanceof Tariff) {
                $id = $tariff->id;
            }
        }

        return $id;
    }

    public function insert()
    {
        $data = $this->form->toArray();

        try {
            $result = Tariff::perform('create', $data);
        } catch (ResponseErrorException $e) {
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
        } catch (ResponseErrorException $e) {
            throw new UnprocessableEntityHttpException($e->getMessage(), 0, $e);
        }

        return true;
    }

    protected function getFormOptions()
    {
        return array_merge([
            'class' => CertificateTariffForm::class,
            'certificateTypes' => Ref::getList('type,certificate', 'hipanel:certificate', [
                'select' => 'id_label',
                'mapOptions' => ['from' => 'id'],
            ]),
        ], parent::getFormOptions());
    }
}
