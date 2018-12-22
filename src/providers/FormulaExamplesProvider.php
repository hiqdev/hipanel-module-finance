<?php

namespace hipanel\modules\finance\providers;

use Yii;

/**
 * Class FormulaExamplesProvider
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FormulaExamplesProvider
{
    public function getGroups()
    {
        $result[] = $this->groupOf(Yii::t('hipanel.finance.price', 'Fixed discount'), $this->fixedDiscountFormulas());
        $result[] = $this->groupOf(Yii::t('hipanel.finance.price', 'Growing discount'), $this->growingDiscountFormulas());
        $result[] = $this->groupOf(Yii::t('hipanel.finance.price', 'Leasing'), $this->leasingFormulas());

        return $result;
    }

    private function fixedDiscountFormulas()
    {
        return [
            sprintf("discount.fixed('10%%').since('%s').reason('because')", date('m.Y')),
            sprintf("discount.fixed('11 USD').since('%s').reason('agreed')", date('m.Y')),
            sprintf("discount.fixed('20%%').till('%s').reason('loyalty')", date('m.Y', strtotime('+6 months'))),
            sprintf("discount.fixed('5 tb').since('%s').reason('bonus')", date('m.Y'))
                => Yii::t('hipanel.finance.price', 'Applicable for overuse prices. The example will compensate up to 5 TB of overuse as discount. Make sure to use appropriate unit, such as <code>items</code>, <code>gbps</code> or <code>hours</code>.'),
            sprintf("discount.fixed('5 tb').as('deposit,compensation').since('%s').reason('bonus')", date('m.Y'))
                => Yii::t('hipanel.finance.price', 'Applicable for overuse prices. Will compensate up to 5 TB of overuse as a separate Compensation bill. You can use any other bill type in option <code>.as()</code>, such as <code>deposit,creditnote</code> or <code>correction,positive</code>.'),
        ];
    }

    private function growingDiscountFormulas()
    {
        return [
            sprintf("discount.since('%s').grows('10pp').every('month').reason('because')", date('m.Y'))
                => Yii::t('hipanel.finance.price', '<code>10pp</code> means "10 percent points". A percent point is the arithmetic difference of two percentages. For example, moving up from 40% to 50% is a 10 percentage point increase, but is an actual 25% percent increase in what is being measured.'),
            sprintf("discount.since('%s').grows('10%%').every('month').max('100%%').reason('because')", date('m.Y')),
            sprintf("discount.since('%s').grows('20 USD').every('2 months').min('30 USD').max('80 USD')", date('m.Y')),
            sprintf("discount.since('%s').grows('1%%').every('1 months').min('5%%').max('25%%')", date('m.Y')),
        ];
    }

    private function leasingFormulas()
    {
        return [
            sprintf("leasing.since('%s').lasts('3 months').reason('TEST')", date('m.Y')),
        ];
    }

    private function groupOf($name, $formulas)
    {
        return ['name' => $name, 'formulas' => $formulas];
    }
}
