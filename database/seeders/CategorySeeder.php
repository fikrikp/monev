<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Plumbing',
            'Civil',
            'Electrical',
            'Electronic',
            'HVAC',
            'Audio Visual',
        ];

        foreach ($categories as $name) {
            Category::create(['category_name' => $name]);
        }
    }
}
