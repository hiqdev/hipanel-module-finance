<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\assets\ConsumptionViewerAsset;
use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\Consumption;
use Yii;
use yii\base\Widget;
use yii\db\ActiveRecordInterface;

class ConsumptionViewer extends Widget
{
    public ActiveRecordInterface $mainObject;

    public ConsumptionConfigurator $configurator;

    public Consumption $consumption;

    public function run(): string
    {
        ConsumptionViewerAsset::register($this->view);

        return $this->render('ConsumptionViewer', [
            'initialData' => [
                'boxTitle' => Yii::t('hipanel:finance', 'Resource consumption'),
                'resources' => ResourceHelper::prepareDetailView($this->consumption->resources),
                'totals' => ResourceHelper::calculateTotal($this->consumption->resources),
                'columns' => $this->configurator->getColumnsWithLabels($this->consumption->class),
                'groups' => $this->configurator->getGroupsWithLabels($this->consumption->class),
                'object_id' => $this->mainObject->id,
                'class' => $this->consumption->class,
            ],
        ]);
    }
}
