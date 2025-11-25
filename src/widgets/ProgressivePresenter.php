<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\helpers\HtmlHelper;
use hipanel\modules\finance\grid\presenters\price\ProgressivePricePresenter;
use hipanel\modules\finance\models\RepresentablePrice;
use hipanel\widgets\DynamicFormWidget;
use Yii;
use yii\base\Widget;

class ProgressivePresenter extends Widget
{
    public RepresentablePrice $price;
    public DynamicFormWidget $dynamicFormWidget;
    public int $index;
    readonly private ProgressivePricePresenter $presenter;

    public function init(): void
    {
        parent::init();
        $this->presenter = Yii::$container->get(ProgressivePricePresenter::class, ['canReadParts' => Yii::$app->user->can('parts.read')]);
    }

    public function run(): string
    {
        $this->applyCss();
        $this->applyJs();

        return HtmlHelper::tag('div', $this->presenter->renderPrice($this->price), ['class' => 'progressive-info bg-info text-white']);
    }

    private function applyCss(): void
    {
        $this->view->registerCss(
            <<<"CSS"
            .progressive-info {
                padding: 3px 15px;
                margin-top: 1rem;
                font-size: small;
            }
            @media (width >= 2300px) {
                .progressive-info {
                    position: absolute;
                    top: -15px;
                }
            }
CSS
        );
    }

    private function applyJs(): void
    {
        $dfContainerClass = '.' . $this->dynamicFormWidget->widgetContainer;
        $this->view->registerJs(
            <<<"JS"
            ;(() => {
              function updateProgressiveInfo(e) {
                const array = $(e.target).closest('.well').find('input, select').serializeArray();
                const data = array.reduce((acc, field) => {
                  acc[field.name] = field.value;

                  return acc;
                }, {});
                $(e.target).closest('.well').find('.progressive-info').load('get-progressive-info', { ...data });
              }
              $("$dfContainerClass").closest(".well").on("change", "input, select", updateProgressiveInfo);
              $("$dfContainerClass").on("afterDelete", updateProgressiveInfo);
            })();
JS
        );
    }
}
