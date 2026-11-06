<?php

use App\Models\Category;

it('returns active categories publicly with the requested fields and timestamps', function () {
    Category::factory()->create([
        'name' => 'Audio',
        'slug' => 'audio',
        'description' => 'Produk audio.',
        'is_active' => true,
        'created_at' => '2025-03-01 10:00:00',
        'updated_at' => '2025-03-02 11:30:00',
    ]);
    Category::factory()->create([
        'name' => 'Aksesori',
        'slug' => 'aksesori',
        'description' => null,
        'is_active' => false,
    ]);

    $this->getJson('/api/categories')
        ->assertOk()
        ->assertExactJson([
            'code' => 200,
            'status' => 'success',
            'message' => 'Data kategori berhasil diambil.',
            'data' => [
                [
                    'id' => 1,
                    'nama' => 'Audio',
                    'slug' => 'audio',
                    'deskripsi' => 'Produk audio.',
                    'created_at' => '2025-03-01T10:00:00.000000Z',
                    'updated_at' => '2025-03-02T11:30:00.000000Z',
                ],
            ],
        ]);
});
