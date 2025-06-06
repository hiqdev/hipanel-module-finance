<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\BillingRegistry;

use hiqdev\php\billing\product\BillingRegistryInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use yii\base\Widget;
use yii\helpers\Html;

class TariffTypesWidget extends Widget
{
    public BillingRegistryInterface $registry;

    public function init()
    {
        parent::init();

        if ($this->registry === null) {
            throw new \InvalidArgumentException('Registry must be provided');
        }
    }

    public function run()
    {
        $content = '';

        foreach ($this->registry->getTariffTypeDefinitions() as $definition) {
            $content .= $this->renderDefinitionSection($definition);
        }

        return Html::tag('div', $content, ['class' => 'tariff-types-container']);
    }

    protected function renderDefinitionSection(TariffTypeDefinitionInterface $definition)
    {
        $title = Html::tag('h2',
            $definition->tariffType()->label() . ': ' .
            Html::tag('code', $definition->tariffType()->name()),
            ['encode' => false]
        );

        $table = TariffPricesTableWidget::widget([
            'tariff' => $definition,
        ]);

        return Html::tag('div', $title . $table, ['class' => 'tariff-type-section']);
    }
}
