<?php declare(strict_types=1);
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
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\ProgressivePrice;
use hipanel\modules\finance\models\TemplatePrice;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use yii\web\User;

/**
 * Class PricePresenterFactory.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PricePresenterFactory
{
    /**
     * @var array map of model class to its presenter
     */
    protected array $map = [
        Price::class => PricePresenter::class,
        TemplatePrice::class => TemplatePricePresenter::class,
        CertificatePrice::class => CertificatePricePresenter::class,
        ProgressivePrice::class => ProgressivePricePresenter::class,
        '*' => PricePresenter::class,
    ];
    protected array $cache = [];

    public function __construct(
        private readonly ContainerInterface $container,
    )
    {
    }

    /**
     * @param string $name
     * @return PricePresenter
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function build(string $name): PricePresenter
    {
        $className = $this->map[$name] ?? $this->map['*'];

        if (!isset($this->cache[$name])) {
            $this->cache[$name] = $this->container->get($className);
        }

        return $this->cache[$name];
    }

    public function getMap(): array
    {
        return $this->map;
    }
}
