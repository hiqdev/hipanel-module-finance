<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid\presenters\price;

use hipanel\modules\finance\models\CertificatePrice;
use hipanel\modules\finance\models\RepresentablePrice;
use Money\MoneyFormatter;
use yii\i18n\Formatter;
use yii\web\User;

/**
 * Class CertificatePricePresenter.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CertificatePricePresenter extends PricePresenter
{
    protected MoneyFormatter $moneyFormatter;

    public function __construct(Formatter $formatter, User $user, MoneyFormatter $moneyFormatter)
    {
        parent::__construct($formatter, $user);

        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * @param CertificatePrice $price
     * @return string
     */
    public function renderPrice(RepresentablePrice $price): string
    {
        $result = [];
        foreach ($price->sums as $amount) {
            $result[] = $this->moneyFormatter->format($amount);
        }

        return implode('&nbsp;/&nbsp;', $result);
    }
}
