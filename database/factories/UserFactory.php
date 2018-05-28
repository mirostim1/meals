<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Language::class, function (Faker $faker) {
    static $id = -1;
    $lang = ['en', 'hr', 'de'];
    $id++;

    return [
        'language' => $lang[$id]
    ];
});

$factory->define(App\Category::class, function (Faker $faker) {
    static $id = 0;
    $id++;
    return [
        'category_id' => $id,
        'slug' => 'category-' . $id
    ];
});

$factory->define(App\Cattranslation::class, function (Faker $faker) {
    static $id = 1;
    static $i = 0;

    $i++;
    if($i > 3) {
        $i = 1;
        $id++;
    }
    $lang = ['1' => 'EN', '2' => 'HR', '3' => 'DE'];

    return [
        'category_id' => $id,
        'language_id' => $i,
        'title' => 'Title of category '. $id . ' ' . $lang[$i]
    ];
});

$factory->define(App\Tag::class, function (Faker $faker) {
    static $tagId = 0;
    static $mealId = 0;
    $tagId++;
    if($tagId > 30) {
       $tagId = 1;
    }
    $mealId++;
    if($mealId > 10) {
        $mealId = 1;
    }
    return [
        'meal_id' => $mealId,
        'slug' => 'tag-' . $tagId
    ];
});

$factory->define(App\Tagtranslation::class, function (Faker $faker) {
    static $id = 1;
    static $i = 0;

    $i++;
    if($i > 3) {
        $i = 1;
        if($id > 30) {
            $id = 0;
        }
        $id++;
    }
    $lang = ['1' => 'EN', '2' => 'HR', '3' => 'DE'];

    return [
        'tag_id' => $id,
        'language_id' => $i,
        'title' => 'Title of tag '. $id . ' ' . $lang[$i]
    ];
});

$factory->define(App\Ingredient::class, function (Faker $faker) {
    static $ingredientId = 0;
    static $mealId = 0;
    $ingredientId++;
    if($ingredientId > 30) {
       $ingredientId = 1;
    }
    $mealId++;
    if($mealId > 10) {
        $mealId = 1;
    }
    return [
        'meal_id' => $mealId,
        'slug' => 'ingredient-' . $ingredientId
    ];
});

$factory->define(App\Ingtranslation::class, function (Faker $faker) {
    static $id = 1;
    static $i = 0;

    $i++;
    if($i > 3) {
        $i = 1;
        if($id > 30) {
            $id = 0;
        }
        $id++;
    }
    $lang = ['1' => 'EN', '2' => 'HR', '3' => 'DE'];

    return [
        'ingredient_id' => $id,
        'language_id' => $i,
        'title' => 'Title of ingredient '. $id . ' ' . $lang[$i]
    ];
});

$factory->define(App\Meal::class, function (Faker $faker) {
    static $categoryId = 0;

    $categoryId++;
    if($categoryId > 5) {
       $categoryId = 1;
    }

    return [
        'category_id' => $categoryId
    ];
});

$factory->define(App\Mealtranslation::class, function (Faker $faker) {
    static $id = 1;
    static $i = 0;

    $i++;
    if($i > 3) {
        $i = 1;
        if($id > 10) {
            $id = 0;
        }
        $id++;
    }
    $lang = ['1' => 'EN', '2' => 'HR', '3' => 'DE'];

    return [
        'meal_id' => $id,
        'language_id' => $i,
        'title' => 'Title of meal '. $id . ' ' . $lang[$i],
        'description' => 'Description of meal ' . $id . ' ' . $lang[$i]
    ];
});
