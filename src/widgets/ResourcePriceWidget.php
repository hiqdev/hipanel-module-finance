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

use Money\Money;
use Money\MoneyFormatter;
use yii\base\Widget;

class ResourcePriceWidget extends Widget
{
    /**
     * @var Money
     */
    public $price;
    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    public function __construct(MoneyFormatter $decimalMoneyFormatter, $config = [])
    {
        parent::__construct($config);
        $this->moneyFormatter = $decimalMoneyFormatter;
    }

    public function run()
    {
        return $this->moneyFormatter->format($this->price);
    }
}
