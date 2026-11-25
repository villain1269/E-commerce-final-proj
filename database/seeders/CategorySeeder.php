<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['key' => 'dough',     'label' => 'Pizza Dough',        'badge' => 'DOUGH',     'sort_order' => 1],
            ['key' => 'bases',      'label' => 'Bases & Crusts',     'badge' => 'BASES',     'sort_order' => 2],
            ['key' => 'boxes',      'label' => 'Packaging',          'badge' => 'PACKAGING', 'sort_order' => 3],
            ['key' => 'equipment',  'label' => 'Equipment',          'badge' => 'EQUIPMENT', 'sort_order' => 4],
            ['key' => 'ready',      'label' => 'Ready-Made Pizza',   'badge' => 'PIZZA',     'sort_order' => 5],
        ];

        foreach ($rows as $row) {
            Category::updateOrCreate(['key' => $row['key']], $row);
        }
    }
}
