<?php

namespace App\Models\Custom;

use Carbon\Carbon;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class MyDecimal implements Cast, Transformer
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): float
    {
        return number_format((float)$value, 2, '.', '');
    }

    public function transform(DataProperty $property, mixed $value, TransformationContext $context): float
    {
        return number_format((float)$value, 2, '.', '');
    }
}
