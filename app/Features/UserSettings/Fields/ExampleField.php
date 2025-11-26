<?php

declare(strict_types=1);

namespace Metricool\Features\UserSettings\Fields;

class ExampleField extends Field
{
    public function getValue(): string
    {
        return "This is an example field";
    }
}
