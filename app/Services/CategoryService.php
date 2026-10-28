<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * @return Collection<int, Category>
     */
    public function getAll(): Collection
    {
        return Category::query()
            ->orderBy('name')
            ->get();
    }

    public function newCategory(): Category
    {
        return new Category(['is_active' => true]);
    }

    /**
     * @param  array{name: string, slug: string, description?: ?string, is_active?: bool|string|int}  $attributes
     */
    public function create(array $attributes): Category
    {
        $attributes['is_active'] = (bool) ($attributes['is_active'] ?? false);

        return Category::create($attributes);
    }

    /**
     * @param  array{name: string, slug: string, description?: ?string, is_active?: bool|string|int}  $attributes
     */
    public function update(Category $category, array $attributes): void
    {
        $attributes['is_active'] = (bool) ($attributes['is_active'] ?? false);

        $category->update($attributes);
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
