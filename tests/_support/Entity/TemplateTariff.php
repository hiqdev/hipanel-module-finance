<?php

namespace hipanel\modules\finance\tests\_support\Entity;

class TemplateTariff
{
    private $data;
    private $name;
    private $price;

    public function __construct (array $data) {
        $this->data = $data;
        $this->name = $data['name'];
        $this->price = $data['price'];
    }

    public function getTemplateData(): array
    {
        return $this->data;
    }

    public function getTemplateName(): string 
    {
        return $this->name;
    }

    public function getTemplatePrice(): array
    {
        return $this->price;
    }

    public function setTemplateId(string $id): void
    {
        $this->data['id'] = $id;
    }

    public function getTemplateId(): ?string
    {
        return $this->data['id'];
    }
}
