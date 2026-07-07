<?php

declare(strict_types=1);

namespace RoundingWell\HL7\Tests;

trait CanProvideMessage
{
    protected function messagePath(string $name): string
    {
        return __DIR__ . "/data/{$name}.txt";
    }

    protected function messageData(string $name): string
    {
        return file_get_contents($this->messagePath($name));
    }
}
