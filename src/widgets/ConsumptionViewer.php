<?php

namespace hipanel\modules\finance\widgets;

use hipanel\helpers\Url;
use hipanel\modules\finance\assets\ConsumptionViewerAsset;
use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\Consumption;
use Yii;
use yii\base\Widget;
use yii\db\ActiveRecordInterface;
use yii\helpers\Html;

class ConsumptionViewer extends Widget
{
    public ActiveRecordInterface $mainObject;
    public ConsumptionConfigurator $configurator;
    public ?Consumption $consumption;
    public string $getConsumptionUrl = '@finance/consumption/get-consumption';
    public bool $showCharts = true;

    public function run(): string
    {
        ConsumptionViewerAsset::register($this->view);
        if (!$this->consumption) {
            return Html::tag('div',
                Yii::t('hipanel:finance', 'No consumption found for the requested resource'),
                ['class' => 'alert alert-warning text-center']);
        }
        $columns = $this->consumption->getColumnsWithLabels();
        if (empty($columns)) {
            return '';
        }

        return $this->render('ConsumptionViewer', [
            'initialData' => [
                'columns' => $columns,
                'boxTitle' => Yii::t('hipanel:finance', 'Resource consumption'),
                'resources' => ResourceHelper::prepareDetailView($this->consumption->resources),
                'totals' => ResourceHelper::calculateTotal($this->consumption->resources),
                'groups' => $this->consumption->getGroupsWithLabels(),
                'object_id' => $this->mainObject->id,
                'class' => $this->consumption->class,
                'getConsumptionUrl' => Url::toRoute($this->getConsumptionUrl),
                'showCharts' => $this->showCharts,
            ],
        ]);
    }
}
