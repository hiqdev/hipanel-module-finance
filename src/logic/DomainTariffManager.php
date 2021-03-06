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

use hipanel\modules\finance\forms\DomainTariffForm;
use hipanel\modules\finance\models\Tariff;
use hiqdev\hiart\ConnectionInterface;
use Yii;
use yii\web\NotFoundHttpException;

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
        parent::init();

        if (!Yii::getAlias('@domain', true)) {
            throw new NotFoundHttpException('Domain module is missing');
        }

        $this->formOptions['zones'] = $this->getZones();
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
        $command = $this->connection->createCommand();

        return $command->perform('getZones', '')->getData();
    }
}
