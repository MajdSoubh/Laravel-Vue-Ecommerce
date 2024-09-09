<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maso\Treeify\Traits\HasTree;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory, HasSlug, HasTree;

    protected $fillable = [
        'name',
        'slug',
        'created_by',
        'updated_by',
        'deleted_by',
        'active',
        'parent_id'
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }


    public static function active()
    {
        return self::query()->where('active', true);
    }


    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category', 'category_id', 'product_id');
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function mainCategory()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
