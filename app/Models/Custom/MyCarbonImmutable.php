<?php

namespace App\Models\Custom;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class MyCarbonImmutable implements Cast, Transformer
{
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): CarbonImmutable
    {
        return new CarbonImmutable($value);
    }

    public function transform(DataProperty $property, mixed $value, TransformationContext $context): string
    {
        return $value->format('Y-m-d H:i:s');
    }
}
