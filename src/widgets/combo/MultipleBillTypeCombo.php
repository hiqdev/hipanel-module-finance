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

    public bool $dontCutType = false;

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
            foreach ($category as $key => $label) {
                if ($this->dontCutType) {
                    $items[$key] = $label;
                } else {
                    $items[substr($key, strpos($key, ',') + 1)] = $label;
                }
            }
            $groupLabel = isset($this->billGroupLabels[$groupType]) ? $this->billGroupLabels[$groupType]['label'] : $groupType;
            $types[$groupLabel] = $items;
        }

        return $types;
    }
}
