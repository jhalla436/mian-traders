<?php

namespace App\Support;

#[\AllowDynamicProperties]
class ProductPresenter
{
    public $id;
    public $name;
    public $sku;
    public $mrp;
    public $max_discount_percent;
    public $effective_discount;
    public $buying_price;
    public $stock_qty;
    public $length_in;
    public $width_in;
    public $height_in;
    public $category;
    public $company;

    public function __construct(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function effectiveMaxDiscountPercent(): float
    {
        return (float)($this->effective_discount ?? 0);
    }
}
