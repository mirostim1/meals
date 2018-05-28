<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Language::class, 3)->create();
        factory(App\Category::class, 5)->create();
        factory(App\Cattranslation::class, 30)->create();
        factory(App\Tag::class, 30)->create();
        factory(App\Tagtranslation::class, 90)->create();
        factory(App\Ingredient::class, 30)->create();
        factory(App\Ingtranslation::class, 90)->create();
        factory(App\Meal::class, 10)->create();
        factory(App\Mealtranslation::class, 30)->create();
    }
}
