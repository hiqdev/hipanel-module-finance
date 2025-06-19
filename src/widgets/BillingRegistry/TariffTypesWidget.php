<?php
declare(strict_types=1);

namespace hipanel\modules\finance\widgets\BillingRegistry;

use hiqdev\php\billing\product\BillingRegistryInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use yii\base\Widget;
use yii\helpers\Html;
use yii\web\View;

class TariffTypesWidget extends Widget
{
    public BillingRegistryInterface $registry;

    public function init()
    {
        parent::init();
        if ($this->registry === null) {
            throw new \InvalidArgumentException('Registry must be provided');
        }
    }

    protected function slugify(string $string): string
    {
        $string = preg_replace('/[^a-zA-Z0-9\s-]/u', '', mb_strtolower((string)$string));
        $string = preg_replace('/[\s-]+/', '-', $string);
        return trim($string, '-');
    }

    public function run()
    {
        $navigationItems = [];
        $mainContent = '';

        $tariffDefinitions = $this->registry->getTariffTypeDefinitions();

        foreach ($tariffDefinitions as $definition) {
            $tariffTypeName = $definition->tariffType()->label() ?: $definition->tariffType()->name();
            $tariffTypeCode = $definition->tariffType()->name();
            $tariffTypeSlug = $this->slugify($tariffTypeCode);
            $tariffTypeId = 'tariff-type--' . $tariffTypeSlug;

            $navTariffItem = [
                'id' => $tariffTypeId,
                'label' => $tariffTypeName . ' (' . $tariffTypeCode . ')',
                'priceTypes' => [],
            ];

            foreach ($definition->withPrices()->getIterator() as $priceType) {
                $priceTypeName = $priceType->type()->getName();
                $priceTypeSlug = $this->slugify($priceTypeName);
                $priceTypeId = 'price-type--' . $tariffTypeSlug . '--' . $priceTypeSlug;
                $navTariffItem['priceTypes'][] = [
                    'id' => $priceTypeId,
                    'label' => $priceTypeName,
                ];
            }
            $navigationItems[] = $navTariffItem;
            $mainContent .= $this->renderDefinitionSection($definition, $tariffTypeId, $tariffTypeSlug);
        }

        $sidebarHtml = TariffNavigationWidget::widget(['navigationItems' => $navigationItems]);

        $this->registerJs();
        $this->registerCss();

        $layout = Html::tag('div',
            Html::tag('div', Html::tag('div', $sidebarHtml, ['class' => 'sticky-sidebar']), ['class' => 'navigation-sidebar-column']) .
            Html::tag('div', $mainContent, ['class' => 'main-content-column']),
            ['class' => 'billing-registry-container']
        );

        return Html::tag('div', $layout, ['class' => 'tariff-types-container-wrapper']);
    }

    protected function renderDefinitionSection(TariffTypeDefinitionInterface $definition, string $tariffTypeId, string $tariffTypeSlug)
    {
        $title = Html::tag('h2',
            Html::encode($definition->tariffType()->label() ?: $definition->tariffType()->name()) . ': ' .
            Html::tag('code', Html::encode($definition->tariffType()->name())),
            ['encode' => false]
        );

        $table = TariffPricesTableWidget::widget([
            'tariff' => $definition,
            'tariffTypeNameSlug' => $tariffTypeSlug,
        ]);

        return Html::tag('div', $title . $table, [
            'class' => 'tariff-type-section',
            'id' => $tariffTypeId,
        ]);
    }

    protected function registerCss()
    {
        $this->view->registerCss(<<<CSS
.billing-registry-container {
    display: flex;
    gap: 25px;
}
.navigation-sidebar-column {
    flex: 0 0 280px;
    position: relative;
}
.main-content-column {
    flex: 1 1 auto;
    min-width: 0;
}
.sticky-sidebar {
    position: sticky;
    top: 60px;
    height: calc(100vh - 60px - 20px);
}

.tariff-type-section h2 {
    scroll-margin-top: 20px;
}
.price-type-card {
    scroll-margin-top: 20px;
}
CSS
        );
    }

    protected function registerJs()
    {
        $this->view->registerJs(<<<JS
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector('.sticky-sidebar');
    if (!sidebar) return;

    // Smooth scrolling for sidebar links
    sidebar.addEventListener('click', function (event) {
        if (event.target.tagName === 'A' && event.target.getAttribute('href').startsWith('#')) {
            event.preventDefault();
            const targetId = event.target.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                history.pushState(null, null, '#' + targetId);
            }
        }
    });

    sidebar.querySelectorAll('.tariff-type-item > .nav-link').forEach(function(tariffLink) {
        const toggler = tariffLink.parentElement.querySelector('.toggler');
        const priceList = tariffLink.parentElement.querySelector('.price-type-list');

        if (toggler && priceList) {
            toggler.addEventListener('click', function(e) {
                e.stopPropagation();
                priceList.classList.toggle('expanded');
                toggler.textContent = priceList.classList.contains('expanded') ? '▾' : '▸';
            });
            
            tariffLink.addEventListener('click', function(e) {
                if (e.target !== toggler && !e.target.closest('.toggler')) {
                     if (priceList && !priceList.classList.contains('expanded')) {
                         priceList.classList.toggle('expanded');
                         toggler.textContent = priceList.classList.contains('expanded') ? '▾' : '▸';
                     }
                }
            });
        }
    });

    const sections = document.querySelectorAll('.main-content-column .tariff-type-section, .main-content-column .price-type-card');
    const navLinks = sidebar.querySelectorAll('.nav-link');

    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -75% 0px',
        threshold: 0
    };

    let lastActivated = null;

    const observer = new IntersectionObserver((entries) => {
        let currentVisibleId = null;

        entries.forEach(entry => {
            if (entry.isIntersecting) {
                currentVisibleId = entry.target.id;
            }
        });

        if (!currentVisibleId) {
            for (const entry of entries) {
                if (entry.boundingClientRect.top > 0 && entry.boundingClientRect.top < window.innerHeight / 4) {
                     currentVisibleId = entry.target.id;
                     break;
                }
            }
        }
        
        if(!currentVisibleId && entries.length > 0) {
            let closestEntry = null;
            let minPositiveTop = Infinity;
            for (const entry of entries) {
                const rect = entry.target.getBoundingClientRect();
                if (rect.top >=0 && rect.top < minPositiveTop) {
                    minPositiveTop = rect.top;
                    closestEntry = entry;
                }
            }
            if (closestEntry) currentVisibleId = closestEntry.target.id;
        }


        if (currentVisibleId) {
            navLinks.forEach(link => {
                link.parentElement.classList.remove('active');
                if (link.getAttribute('href') === '#' + currentVisibleId) {
                    link.parentElement.classList.add('active');
                    
                    const parentTariffItem = link.closest('.tariff-type-item');
                    if (parentTariffItem && link.closest('.price-type-list')) {
                        const priceList = parentTariffItem.querySelector('.price-type-list');
                        const toggler = parentTariffItem.querySelector('.toggler');
                        if (priceList && !priceList.classList.contains('expanded')) {
                            priceList.classList.add('expanded');
                            if(toggler) toggler.textContent = '▾';
                        }
                    }
                    lastActivated = link.parentElement;
                }
            });
        } else if (lastActivated && entries.every(e => !e.isIntersecting)) {
        }


    }, observerOptions);

    sections.forEach(section => {
        observer.observe(section);
    });
});
JS,
        View::POS_END); // Ensure JS runs after DOM is ready
    }
}
