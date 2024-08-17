<?php declare(strict_types=1);

namespace hipanel\modules\finance\models;

use Yii;
use yii\base\Model;

/**
 *
 * @property-read string $unitLabel
 * @property-read bool $isNewRecord
 */
class Threshold extends Model
{
    public ?string $price = null;
    public ?string $currency = null;
    public ?string $quantity = null;
    public ?string $unit = null;
    private ?Price $parent = null;

    public function attributeLabels(): array
    {
        return array_merge(parent::rules(), [
            'quantity' => Yii::t('hipanel:finance', 'Threshold'),
        ]);
    }

    public function getIsNewRecord(): bool
    {
        return empty($this->price);
    }

    public function getUnitLabel(): ?string
    {
        return $this->parent->getUnitLabel();
    }

    public function getCurrencyLabel()
    {
        return mb_strtoupper($this->currency);
    }

    public function setParent(?Price $parent): void
    {
        $this->parent = $parent;
    }
}
