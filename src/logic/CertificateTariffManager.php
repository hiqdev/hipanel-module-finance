<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\logic;

use hipanel\models\Ref;
use hipanel\modules\finance\forms\CertificateTariffForm;
use hipanel\modules\finance\models\Tariff;
use hiqdev\hiart\ConnectionInterface;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Class CertificateTariffManager.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CertificateTariffManager extends AbstractTariffManager
{
    /**
     * @var CertificateTariffForm
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
