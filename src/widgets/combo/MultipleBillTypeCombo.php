<?php

namespace hipanel\modules\finance\widgets\combo;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\widgets\BillTypeVueTreeSelect;
use hiqdev\combo\StaticCombo;
use yii\helpers\Json;
use yii\web\JsExpression;

/**
 * Class MultipleBillTypeCombo
 * @package hipanel\modules\finance\widgets\combo
 * @deprecated Use {@see BillTypeVueTreeSelect} instead
 */
class MultipleBillTypeCombo extends StaticCombo
{
    /**
     * @var array
     */
    public $billTypes;

    /**
     * @var array
     */
    public $billGroupLabels;

    /**
     * @inheritDoc
     */
    public $hasId = true;

    /**
     * @inheritDoc
     */
    public $multiple = true;

    public bool $isFlatList = false;

    public bool $useFullType = false;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();

        $this->data = $this->isFlatList ? $this->getFlatData() : $this->getData();
    }

    public function getPluginOptions($options = [])
    {
        $options = ArrayHelper::merge(parent::getPluginOptions(), $options);
        if (!$this->isFlatList) {
            return $options;
        }
        $labels = Json::encode(array_values(array_map(static fn($it) => $it['label'], $this->billGroupLabels)));
        $options['select2Options']['templateResult'] = new JsExpression("
            function (data) {
                if (data.loading) {
                    return data.text;
                }
                const lbs = $labels;
                if (!lbs.includes(data.text)) {
                    return data.text;
                }
                const wrapper = $('<strong></strong>');

                return wrapper.text(data.text);
            }
        ");

        return $options;
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        $types = [];
        foreach ($this->billTypes as $groupType => $category) {
            $items = [];
            foreach ($category as $type => $label) {
                [, $name] = explode(',', $type);
                $type = $this->useFullType ? $type : $name;
                $items[$type] = $label;
            }
            $groupLabel = $this->billGroupLabels[$groupType]['label'] ?? $groupType;
            $types[$groupLabel] = $items;
        }

        return $types;
    }

    private function getFlatData(): array
    {
        $types = [];
        foreach ($this->billTypes as $groupType => $category) {
            $groupLabel = $this->billGroupLabels[$groupType]['label'] ?? $groupType;
            $types[$groupType] = $groupLabel;
            foreach ($category as $type => $label) {
                [, $name] = explode(',', $type);
                $type = $this->useFullType ? $type : $name;
                $types[$type] = $label;
            }
        }

        return $types;
    }
}
