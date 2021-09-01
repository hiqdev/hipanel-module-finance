<?php
declare(strict_types=1);

namespace hipanel\modules\finance\tests\_support\Entity;

use hipanel\modules\finance\tests\_support\Entity\TemplateTariff;

class Tariff
{
    public ?int $id = null;
    public string $name;
    public string $type;
    public string $client;
    public string $currency;
    public string $note;
    public array $typeDropDownElements;
    public array $price;
    public ?TemplateTariff $template = null;

    public function __construct(
        string $name,
        string $type,
        string $client,
        string $currency,
        string $note,
        array $price,
        ?TemplateTariff $template = null
    )
    {
        $this->name = $name;
        $this->type = $type;
        $this->client = $client;
        $this->currency = $currency;
        $this->note = $note;
        $this->price = $price;

        if (isset($template)) {
            $this->template = $template;
            $this->price['plan'] = $this->template->name;
        }
    }
}
