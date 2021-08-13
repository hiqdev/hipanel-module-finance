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

    public function __construct (array $data)
    {
        $this->fromArray($data, $this);
    }

    public static function fromArray(array $tariffArray, Tariff $tariff): void
    {
        $tariff->name = $tariffArray['name'] ?? 'tariff name-' . uniqid();
        $tariff->type = $tariffArray['type'] ?? 'server';
        $tariff->client = $tariffArray['client'] ?? 'hipanel_test_reseller';
        $tariff->currency = $tariffArray['currency'] ?? 'USD';
        $tariff->note = $tariffArray['note'] ?? 'tariff note-' . uniqid();
        $tariff->typeDropDownElements = $tariffArray['typeDropDownElements'] ?? [];
        $tariff->price = $tariffArray['price'] ?? [];

        if (isset($tariffArray['template'])) { 
            $tariff->template = $tariffArray['template'];
            $tariff->data['price']['plan'] = $tariff->template->getName();
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
