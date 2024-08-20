<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Category extends Model
{
    use HasFactory, HasSlug;

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

    /**
     * @param int $active Set the type of the requested categories (active -> 1 not-active -> 0 both -> 2)
     * @param id $categoryId
     */
    public static function getAsTree(int $active = 2, $categoryId = null)
    {
        $categories = match ($active)
        {
            0 => Category::where('active', false)->orderBy('parent_id')->get(),
            1 => Category::where('active', true)->orderBy('parent_id')->get(),
            2 => Category::orderBy('parent_id')->get(),
        };

        return self::buildCategoriesTree($categories, $categoryId);
    }

    public static function buildCategoriesTree($categories, $categoryId = null)
    {
        $result = [];
        foreach ($categories as $category)
        {
            if ($category->parent_id === $categoryId)
            {
                $children = self::buildCategoriesTree($categories, $category->id);
                if ($children)
                {
                    $category->setAttribute('children', $children);
                }
                $result[] = $category;
            }
        }
        return $result;
    }
}
