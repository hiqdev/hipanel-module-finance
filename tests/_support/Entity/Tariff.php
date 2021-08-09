<?php

namespace hipanel\modules\finance\tests\_support\Entity;

use hipanel\modules\finance\tests\_support\Entity\TemplateTariff;

class Tariff
{
    private string $name;
    private string $type;
    private string $client;
    private string $currency;
    private string $note;
    private array $typeDropDownElements;
    private array $price;
    public TemplateTariff $template;

    public function __construct (array $data, TemplateTariff $template = null) {
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->client = $data['client'];
        $this->currency = $data['currency'];
        $this->note = $data['note'];
        $this->typeDropDownElements = $data['typeDropDownElements'];
        $this->price = $data['price'];
        if (isset($template)) { 
            $this->template = $template;
            $this->data['price']['plan'] = $this->template->getName();
        }
    }

    public function getData(): array 
    {
        return [
            'name'                 => $this->name,
            'type'                 => $this->type,
            'client'               => $this->client,
            'currency'             => $this->currency,
            'note'                 => $this->note,
            'typeDropDownElements' => $this->typeDropDownElements,
            'price'                => $this->price,
        ];
    }

    public function getName(): ?string 
    {
        return $this->name;
    }

    public function getPrice(): array
    {
        return $this->price;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getClient(): string
    {
        return $this->client;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setTemplateName(string $name): void
    {
        $this->price['plan'] = $name;
    }
}
