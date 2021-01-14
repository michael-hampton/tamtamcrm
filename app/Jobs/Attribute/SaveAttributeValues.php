<?php

namespace App\Jobs\Attribute;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Repositories\AttributeValueRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class SaveAttributeValues
 * @package App\Jobs\Attribute
 */
class SaveAttributeValues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Attribute
     */
    private Attribute $attribute;

    /**
     * @var array
     */
    private array $attribute_values;

    /**
     * SaveAttributeValues constructor.
     * @param Attribute $attribute
     * @param $attribute_values
     */
    public function __construct(Attribute $attribute, $attribute_values)
    {
        $this->attribute = $attribute;
        $this->attribute_values = $attribute_values;
    }

    /**
     * @return bool
     */
    public function handle(): bool
    {
        $this->attribute->values()->forceDelete();

        foreach ($this->attribute_values as $value) {
            $attribute_value = new AttributeValue();
            $attribute_value->fill($value);
            $attribute_value_repo = new AttributeValueRepository($attribute_value);

            $attribute_value_repo->associateToAttribute($this->attribute);
        }

        return true;
    }
}