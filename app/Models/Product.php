<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Tag;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Stock;

class Product extends Model
{
    use HasFactory;


    protected $guarded = [];

    protected $casts = [
        'images' => 'array',
    ];


    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag', 'product_id', 'tag_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'product_id', 'id');
    }

}
