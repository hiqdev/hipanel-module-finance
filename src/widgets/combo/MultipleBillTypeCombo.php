<?php


namespace hipanel\modules\finance\widgets\combo;


use hiqdev\combo\StaticCombo;

/**
 * Class MultipleBillTypeCombo
 * @package hipanel\modules\finance\widgets\combo
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

    public bool $useFullType = false;

    /**
     * @inheritDoc
     */
    public function init(): void
    {
        parent::init();

        $this->data = $this->getData();
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
}
