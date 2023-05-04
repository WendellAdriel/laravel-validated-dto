<?php

declare(strict_types=1);

namespace WendellAdriel\ValidatedDTO\Validations;

use Illuminate\Validation\ValidationRuleParser;
use WendellAdriel\ValidatedDTO\Casting\Castable;

class DtoValidationParser extends ValidationRuleParser
{
    protected function prepareRule($rule, $attribute)
    {
        if ($rule instanceof Castable) {
            return $rule;
        }

        return parent::prepareRule($rule, $attribute);
    }
}
