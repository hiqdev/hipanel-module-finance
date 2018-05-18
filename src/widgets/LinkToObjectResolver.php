<?php

namespace hipanel\modules\finance\widgets;

use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class LinkToObjectResolver
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 * @author Andrey Klockok <tofid@hiqdev.com>
 */
class LinkToObjectResolver extends Widget
{
    /** @var Model */
    public $model;

    /** @var string */
    public $labelAttribute = 'object';

    /** @var string  */
    public $typeAttribute = 'type';

    /** @var string  */
    public $idAttribute = 'id';

    public $linkOptions = [
        'class' => 'text-bold',
    ];

    private $links = [
        'ip' => '@ip/view',
        'client' => '@client/view',
        'account' => '@account/view',
        'server' => '@server/view',
        'device' => '@server/view',
        'part' => '@part/view',
        'tariff' => '@plan/view',
        'model_group' => '@model-group/view',
    ];

    /**
     * @return string
     */
    public function run()
    {
        $label = $this->getLabel();
        if ($label === null) {
            return '';
        }

        $link = $this->getLink();
        if ($link === null) {
            return $this->getLabel();
        }

        return Html::a($this->getLabel(), $this->getLink(), $this->linkOptions);
    }

    /**
     * @return string
     */
    private function getLabel()
    {
        return $this->model->{$this->labelAttribute};
    }

    /**
     * @return array|null
     */
    private function getLink()
    {
        $type = $this->model->{$this->typeAttribute};
        if (!isset($this->links[$type])) {
            return null;
        }
        if (!isset($this->model->{$this->idAttribute})) {
            return null;
        }

        return [$this->links[$type], 'id' => $this->model->{$this->idAttribute}];
    }
}
