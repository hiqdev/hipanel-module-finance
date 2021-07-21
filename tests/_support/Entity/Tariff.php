<?php

namespace hipanel\modules\finance\tests\_support\Entity;

use hipanel\modules\finance\tests\_support\Entity\TemplateTariff;

class Tariff
{
    private $data;
    private $name;
    private TemplateTariff $template;

    public function __construct (array $data, TemplateTariff $template = null) {
        $this->data = $data;
        $this->name = $data['name'];
        if (isset($template)) { 
            $this->template = $template;
            $this->data['price']['plan'] = $this->template->getTemplateName();
        }
    }

    public function getTariffData(): array
    {
        return $this->data;
    }

    public function getTariffName(): ?string 
    {
        return $this->name;
    }

    public function setTariffId(string $id): void
    {
        $this->data['id'] = $id;
    }

    public function getTariffId(): ?string
    {
        return $this->data['id'];
    }

    public function getTariffPrice(): array
    {
        return $this->data['price'];
    }

    public function getTemplateName(): ?string
    {
        return $this->template->getTemplateName();
    }

    public function getTemplateData(): array
    {
        return $this->template->getTemplateData();
    }

    public function getTemplatePrice(): array
    {
        return $this->template->getTemplatePrice();
    }

    public function setTemplateId(string $id): void
    {
        $this->template->setTemplateId($id);
    }

    public function getTemplateId(): ?string
    {
        return $this->template->getTemplateId();
    }
}
