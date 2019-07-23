<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\base\Model;
use yii\base\Widget;
use yii\helpers\Html;

/**
 * Class LinkToObjectResolver.
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

    /** @var string */
    public $typeAttribute = 'type';

    /** @var string */
    public $idAttribute = 'id';

    public $linkOptions = [
        'class' => 'text-bold',
    ];

    /**
     * Custom links for $links. For example:.
     *
     * ```php
     * 'customLinks' => [
     *   'part' => '@server/view',
     * ]
     * ```
     */
    public $customLinks = [];

    private $links = [
        'ip' => '@ip/view',
        'client' => '@client/view',
        'account' => '@account/view',
        'server' => '@server/view',
        'pcdn' => '@server/view',
        'vcdn' => '@server/view',
        'device' => '@server/view',
        'part' => '@part/view',
        'tariff' => '@plan/view',
        'switch' => '@hub/view',
        'model_group' => '@model-group/view',
    ];

    /**
     * @return string
     */
    public function run()
    {
        foreach ($this->customLinks as $link => $path) {
            $this->links[$link] = $path;
        }
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
        if (!Yii::getAlias($this->links[$type], false)) {
            return null;
        }
        if (!isset($this->links[$type])) {
            return null;
        }
        if (!isset($this->model->{$this->idAttribute})) {
            return null;
        }

        return [$this->links[$type], 'id' => $this->model->{$this->idAttribute}];
    }
}
