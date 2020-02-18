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

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $this->data = $this->getData();
        $this->inputOptions[] = [
            'groups' => $this->billGroupLabels,
        ];
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        $types = [];
        foreach ($this->billTypes as $gtype => $category) {
            $item = [];
            foreach ($category as $key => $label) {
                $item[substr($key, strpos($key, ',') + 1)] = $label;
            }
            $types[$gtype] = $item;
        }
        return $types;
    }
}
