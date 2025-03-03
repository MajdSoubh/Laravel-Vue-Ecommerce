<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter
{
    public function apply(Builder $query, array $filters): Builder
    {
        foreach ($filters as $filter => $value)
        {
            if (method_exists($this, $filter) && !empty($value))
            {
                $this->$filter($query, $value);
            }
        }
        return $query;
    }

    protected function search(Builder $query, string $search): void
    {
        $query->where('title', 'like', "%{$search}%");
    }

    protected function categories(Builder $query, array $categories): void
    {
        $query->whereHas('categories', function ($query) use ($categories)
        {
            $query->where('active', true)
                ->where(function ($query) use ($categories)
                {
                    $query->whereIn('name', $categories)
                        ->orWhereHas('mainCategory', function ($query) use ($categories)
                        {
                            $query->where('active', true)
                                ->whereIn('name', $categories);
                        });
                });
        });
    }
}
