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
            sprintf("discount.since('%s').fixed('10%%').reason('because')", date('m.Y')),
            sprintf("discount.since('%s').fixed('11 USD').reason('agreed')", date('m.Y')),
            sprintf("discount.fixed('20%%').till('%s').reason('loyalty')", date('m.Y', strtotime('+6 months'))),
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
