<?php

namespace hipanel\modules\finance\helpers\parser\parsers;

interface ParserInterface
{
    public function getSum(): ?float;

    public function getFee(): ?float;

    public function getNet(): ?float;

    public function getCurrency(): ?string;

    public function getQuantity(): ?int;

    public function getUnit(): ?string;

    public function getTime(): ?string;

    public function getClient(): ?string;

    public function getTxn(): ?string;

    public function getLabel(): ?string;

    public function getClientSubstrings(): ?array;

    public function findClient(string $str): ?string;
}
