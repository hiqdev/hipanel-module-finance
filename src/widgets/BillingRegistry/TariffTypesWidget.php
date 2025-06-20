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

        $behaviors = $this->renderBehaviors($definition);

        $table = TariffPricesTableWidget::widget([
            'tariff' => $definition,
            'tariffTypeNameSlug' => $tariffTypeSlug,
        ]);

        return Html::tag('div', $title . $behaviors . $table, [
            'class' => 'tariff-type-section',
            'id' => $tariffTypeId,
        ]);
    }

    protected function renderBehaviors(TariffTypeDefinitionInterface $definition): string
    {
        $behaviors = [];
        
        try {
            foreach ($definition->withBehaviors() as $behavior) {
                $behaviorClass = get_class($behavior);
                $behaviorName = basename(str_replace('\\', '/', $behaviorClass));
                
                $description = '';
                if (method_exists($behavior, 'description')) {
                    $description = $behavior->description();
                }
                
                $behaviors[] = [
                    'name' => $behaviorName,
                    'class' => $behaviorClass,
                    'description' => $description,
                ];
            }
        } catch (\Exception) {
            // Skip behaviors rendering if there's an error accessing them
            return '';
        }

        if (empty($behaviors)) {
            return '';
        }

        $behaviorItems = [];
        foreach ($behaviors as $behavior) {
            $item = Html::tag('div', 
                Html::tag('strong', Html::encode($behavior['name']), ['class' => 'behavior-name']) .
                Html::tag('small', Html::encode($behavior['class']), ['class' => 'behavior-class']) .
                ($behavior['description'] ? Html::tag('p', $behavior['description'], ['class' => 'behavior-description']) : ''),
                ['class' => 'behavior-item']
            );
            $behaviorItems[] = $item;
        }

        return Html::tag('div',
            Html::tag('h3', 'Behaviors', ['class' => 'behaviors-title']) .
            Html::tag('div', implode('', $behaviorItems), ['class' => 'behaviors-list']),
            ['class' => 'tariff-behaviors-section']
        );
    }

    protected function registerCss(): void
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

.tariff-type-section {
    border-top: 1px dashed #565574;
    padding-top: 2em;
}
.tariff-type-section h2 {
    scroll-margin-top: 20px;
}
.price-type-card {
    scroll-margin-top: 20px;
}

/* Behavior Styles */
.tariff-behaviors-section {
    margin: 1.5em 0;
    padding: 1em;
    background-color: #f8f9fa;
    border: 1px solid #e1e5e9;
    border-radius: 6px;
}

.behaviors-title {
    margin: 0 0 1em 0;
    font-size: 1.1em;
    color: #495057;
    font-weight: 600;
}

.behaviors-list {
    display: flex;
    flex-direction: column;
    gap: 0.75em;
}

.behavior-item {
    padding: 0.75em;
    background-color: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.behavior-name {
    display: block;
    color: #007bff;
    font-size: 0.95em;
    margin-bottom: 0.25em;
}

.behavior-class {
    display: block;
    color: #6c757d;
    font-size: 0.8em;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    margin-bottom: 0.5em;
}

.behavior-description {
    margin: 0.5em 0 0 0;
    color: #495057;
    font-size: 0.9em;
    line-height: 1.4;
}
CSS
        );
    }

    protected function registerJs(): void
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
