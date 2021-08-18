<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\_support\Entity;

use hipanel\modules\finance\tests\_support\Entity\TemplateTariff;


class Tariff
{
    const DEFAULT_TYPE = 'Server';
    const DEFAULT_CLIENT = 'hipanel_test_reseller';
    const DEFAULT_CURRENCY = 'USD';

    public string $name;
    public string $type;
    public string $client;
    public string $currency;
    public string $note;
    public array $typeDropDownElements;
    public array $price;
    public int $id;
    public ?TemplateTariff $template = null;

    public function __construct(
        string $name,
        string $type = null,
        string $client = null,
        string $currency = null,
        string $note = null,
        array $price = null,
        ?TemplateTariff $template = null
    )
    {
        $this->name = $name;
        $this->type = $type ?? self::DEFAULT_TYPE;
        $this->client = $client ?? self::DEFAULT_CLIENT;
        $this->currency = $currency ?? self::DEFAULT_CURRENCY;
        $this->note = $note ?? 'tariff note-' . uniqid();
        $this->price = $price ?? ['type' => 'Main prices'];

        if (isset($template)) {
            $this->template = $template;
            $this->price['plan'] = $this->template->name;
        }
    }
}
