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

use hiqdev\yii2\merchant\widgets\PayButton;
use yii\base\Event;
use yii\base\Widget;

class PayButtonComment extends Widget
{
    /**
     * @var Event
     */
    protected $event;

    /**
     * @var array
     */
    public $commentViews = [];

    /**
     * PayButtonCommentHandler constructor.
     * @param Event $event The original event, triggered by [[hiqdev\yii2\merchant\widgets\PayButton]]
     * @param array $config
     */
    public function __construct(Event $event, $config = [])
    {
        parent::__construct($config);

        $this->event = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        $this->commentViews = array_merge($this->getDefaultCommentViews(), $this->commentViews);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->renderComment();
    }

    /**
     * Array of default comment views.
     * @return array
     */
    protected function getDefaultCommentViews()
    {
        return [
            'paypal*' => 'paypal',
            'bitpay*' => 'bitpay',
            'paxum*' => 'paxum',
            'yandexmoney*' => 'yandexmoney',
        ];
    }

    /**
     * Returns the view name for the specified $merchant.
     * @param string $merchant
     * @return string|null
     * @see commentViews
     */
    public function getCommentView($merchant)
    {
        foreach ($this->commentViews as $pattern => $view) {
            if (fnmatch($pattern, $merchant)) {
                return $view;
            }
        }

        return null;
    }

    /**
     * Method renders comment from the view, specified in.
     * @return string
     */
    protected function renderComment()
    {
        $merchant = $this->getMerchantName();

        if (($view = $this->getCommentView($merchant)) === null) {
            return '';
        }

        try {
            return $this->render($view, [
                'merchant' => $merchant,
                'widget' => $this,
                'event' => $this->event,
            ]);
        } catch (\yii\base\ViewNotFoundException $e) {
            return '';
        }
    }

    /**
     * Method provides the merchant name.
     * @return string
     */
    protected function getMerchantName()
    {
        return $this->event->sender->getMerchantSystem();
    }

    /**
     * @return string the view path that may be prefixed to a relative view name
     */
    public function getViewPath()
    {
        return parent::getViewPath() . DIRECTORY_SEPARATOR . 'payButtonComments';
    }

    /**
     * @return PayButton
     */
    public function getPayButton()
    {
        return $this->event->sender;
    }
}
