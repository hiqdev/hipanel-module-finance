<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\providers;

use Yii;

/**
 * Class FormulaExamplesProvider.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FormulaExamplesProvider
{
    private string $currencyCode;

    public function __construct(string $currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    public function getGroups()
    {
        $result[] = $this->groupOf(Yii::t('hipanel.finance.price', 'Fixed discount'), $this->fixedDiscountFormulas());
        $result[] = $this->groupOf(Yii::t('hipanel.finance.price', 'Growing discount'), $this->growingDiscountFormulas());
        $result[] = $this->groupOf(Yii::t('hipanel.finance.price', 'Installment'), $this->installmentFormulas());
        $result[] = $this->groupOf(Yii::t('hipanel.finance.price', 'Monthly Cap'), $this->monthlyCapFomulas());

        return $result;
    }

    private function fixedDiscountFormulas()
    {
        return [
            sprintf("discount.fixed('10%%').since('%s').reason('because')", date('m.Y')),
            sprintf("discount.fixed('11 %s').since('%s').reason('agreed')", $this->currencyCode, date('m.Y')),
            sprintf("discount.fixed('20%%').till('%s').reason('loyalty')", date('m.Y', strtotime('+6 months'))),
            sprintf("discount.fixed('5 tb').since('%s').reason('bonus')", date('m.Y')) => Yii::t('hipanel.finance.price', 'Applicable for overuse prices. The example will compensate up to 5 TB of overuse as discount. Make sure to use appropriate unit, such as <code>items</code>, <code>gbps</code> or <code>hours</code>.'),
            sprintf("discount.fixed('5 tb').as('deposit,compensation').since('%s').reason('bonus')", date('m.Y')) => Yii::t('hipanel.finance.price', 'Applicable for overuse prices. Will compensate up to 5 TB of overuse as a separate Compensation bill. You can use any other bill type in option <code>.as()</code>, such as <code>deposit,creditnote</code> or <code>correction,positive</code>.'),
        ];
    }

    private function growingDiscountFormulas()
    {
        return [
            sprintf("discount.since('%s').grows('10pp').every('month').reason('because')", date('m.Y')) => Yii::t('hipanel.finance.price', '<code>10pp</code> means "10 percent points". A percent point is the arithmetic difference of two percentages. For example, moving up from 40% to 50% is a 10 percentage point increase, but is an actual 25% percent increase in what is being measured.'),
            sprintf("discount.since('%s').grows('10%%').every('month').max('100%%').reason('because')", date('m.Y')),
            sprintf("discount.since('%s').grows('20 %s').every('2 months').min('30 %s').max('80 %s')", date('m.Y'), $this->currencyCode, $this->currencyCode, $this->currencyCode),
            sprintf("discount.since('%s').grows('1%%').every('1 months').min('5%%').max('25%%')", date('m.Y')),
            sprintf("increase.since('%s').grows('10%%').every('year')", date('m.Y')) => Yii::t('hipanel.finance.price', 'The example will <b>increase</b> the price by 10% every year instead of decreasing it. You can use all the same options as for <code>discount</code> formula such as <code>min</code>, <code>max</code>, <code>reason</code>.'),
        ];
    }

    private function installmentFormulas()
    {
        return [
            sprintf("installment.since('%s').lasts('3 months').reason('TEST')", date('m.Y')),
        ];
    }

    private function monthlyCapFomulas()
    {
        return [
            "cap.monthly('28 days')" => Yii::t('hipanel.finance.price',
                'The given formula is useful only for monthly prices. When the monthly invoice consumption exceeds the given cap, two charges will be produced: first at the price amount for 28 days, and second for 0 cents for the rest days of month.'
            ),
        ];
    }

    private function groupOf($name, $formulas)
    {
        return ['name' => $name, 'formulas' => $formulas];
    }
}
