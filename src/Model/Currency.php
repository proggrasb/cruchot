<?php

declare(strict_types=1);

namespace App\Model;

use JsonSerializable;

class Currency implements JsonSerializable
{
    public function __construct(
        private readonly string $code,
        private readonly string $symbolCode,
        private readonly string $name
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getSymbolCode(): string
    {
        return $this->symbolCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): mixed
    {
        return get_object_vars($this);
    }
}
