<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Product;

class Tag extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag', 'product_id', 'tag_id');
    }
}
