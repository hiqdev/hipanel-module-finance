<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\BillingRegistry;

use yii\base\Widget;
use yii\helpers\Html;

class TariffNavigationWidget extends Widget
{
    /**
     * @var array Structure:
     * [
     *     [
     *         'id' => 'tariff-type--slugified-tariff-name',
     *         'label' => 'Tariff Type Display Name',
     *         'priceTypes' => [
     *             ['id' => 'price-type--slugified-tariff-name--slugified-price-name', 'label' => 'Price Type Display Name'],
     *         ]
     *     ],
     * ]
     */
    public array $navigationItems = [];

    public function run(): string
    {
        if (empty($this->navigationItems)) {
            return '';
        }

        $this->view->registerCss(<<<CSS
.sticky-sidebar {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.tariff-navigation-menu ul {
    list-style-type: none;
    padding-left: 0;
}

.tariff-navigation-menu > ul > li > a {
    font-weight: bold;
    display: inline-block;
    padding: 8px 10px;
    text-decoration: none;
    color: #337ab7;
}

.tariff-navigation-menu > ul > li > a:hover,
.tariff-navigation-menu .nav-item a:hover {
    background-color: #f0f0f0;
    text-decoration: none;
}

.tariff-navigation-menu .nav-item.active > a {
    background-color: #e7f3ff;
    border-left: 3px solid #337ab7;
    padding-left: 7px;
}

.tariff-navigation-menu .price-type-list {
    padding-left: 20px;
    display: none;
}

.tariff-navigation-menu .price-type-list.expanded {
    display: block;
}

.tariff-navigation-menu .price-type-list li a {
    display: block;
    padding: 5px 10px;
    text-decoration: none;
    color: #555;
    font-size: 0.9em;
}

.tariff-navigation-menu .toggler {
    cursor: pointer;
    display: inline-block;
    width: 20px;
    text-align: center;
    margin-right: 5px;
    user-select: none;
}
CSS
        );

        $itemsHtml = '';
        foreach ($this->navigationItems as $tariffItem) {
            $priceTypesHtml = '';
            if (!empty($tariffItem['priceTypes'])) {
                $priceTypeLinks = '';
                foreach ($tariffItem['priceTypes'] as $priceItem) {
                    $priceTypeLinks .= Html::tag(
                        'li',
                        Html::a(Html::encode($priceItem['label']), '#' . $priceItem['id'], ['class' => 'nav-link']),
                        ['class' => 'nav-item price-type-item']
                    );
                }
                $priceTypesHtml = Html::tag('ul', $priceTypeLinks, ['class' => 'price-type-list']);
            }

            $toggler = !empty($tariffItem['priceTypes']) ? Html::tag('span', '▸', ['class' => 'toggler']) : '';
            $tariffLink = $toggler . Html::a(Html::encode($tariffItem['label']), '#' . $tariffItem['id'], ['class' => 'nav-link']);

            $itemsHtml .= Html::tag('li', $tariffLink . $priceTypesHtml, ['class' => 'nav-item tariff-type-item']);
        }

        return Html::tag('nav', Html::tag('ul', $itemsHtml), ['class' => 'tariff-navigation-menu']);
    }
}
