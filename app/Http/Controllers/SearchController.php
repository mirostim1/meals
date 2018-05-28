<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\SearchRequest;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function index(SearchRequest $request)
    {
        /**
         * Lang parameter
         */
        $lang = strtolower($request->input('lang'));
        $lang == 'en' ? $langId = 1 : '';
        $lang == 'hr' ? $langId = 2 : '';
        $lang == 'de' ? $langId = 3 : '';

        /**
         * Category parameter
         */
        if($request->has('category')) {
            if($request->input('category') != null) {
                $category = $request->input('category');
            } else {
                $category = 'null';
            }
        } else {
            $category = 'all';
        }

        /**
         * Tags array parameter
         */
        $tags = $request->input('tags');
        $tags ? $tags = array_map('intval', explode(',', $tags)) : '';

        /**
         * Diff_time parameter
         */
        $diffTime = $request->input('diff_time');

        /**
         * Per_page parameter
         */
        $request->input('per_page') ? $perPage = (int) $request->input('per_page') : '';

        /**
         * Page parameter
         */
        $request->input('page') ? $page = (int) $request->input('page') : '';

        /**
         * With array parameter
         */
        $with = $request->input('with');
        $with = explode(',', $with);

        /**
         * Diff_time filtering
         */
        if(isset($diffTime)) {
            $mealsQuery = $this->diffTimeFiltering($diffTime);
        } else {
            $mealsQuery = $this->diffTimeFiltering();
        }

        /**
         * Sorted list of all meals
         */
        $mealsList = $this->sortedListOfAllMeals($mealsQuery, $langId);

        /**
         * Language filtering and making array of meals
         */
        $meals = $this->filteringMealsByLanguage($mealsList, $mealsQuery, $langId, $date = null);

        /**
         * Category filtering
         */
        if(isset($category)) {
            $meals = $this->filteringMealsByCategory($meals, $category);
        }

        /**
         * Tags filtering
         */
        if(isset($tags)) {
            $meals = $this->filteringMealsByTags($meals, $tags);
        }

        /**
         * Page setting
         */
        isset($page) ? $currentPage = $page : $currentPage = 1;

        /**
         * Per page and links settings
         */
        if(isset($perPage)) {
            $paginationData = $this->paginateMeals($meals, $perPage, $currentPage)->toArray();
        } else {
            $perPage = 15; /** default pagination set to 15 */
            $paginationData = $this->paginateMeals($meals, $perPage, $currentPage)->toArray();
        }

        $totalItems = $paginationData['total'];
        $itemsPerPage = $paginationData['per_page'];
        $totalPages =  $paginationData['last_page'];

        $queryString = urldecode($request->getQueryString());
        if(strpos($queryString, '&page='.$currentPage)) {
            $newQueryString = str_replace('&page='.$currentPage, '', $queryString);
        }

        if($paginationData['prev_page_url'] != null) {
            if(isset($newQueryString)) {
                $prev = $request->url().$paginationData['prev_page_url'].'&'.$newQueryString;
            } else {
                $prev = $request->url().$paginationData['prev_page_url'].'&'.$queryString;
            }
        } elseif($paginationData['prev_page_url'] == null) {

            $prev = null;
        }

        if($paginationData['next_page_url'] != null) {
            if(isset($newQueryString)) {
                $next = $request->url().$paginationData['next_page_url'].'&'.$newQueryString;
            } else {
                $next = $request->url().$paginationData['next_page_url'].'&'.$queryString;
            }
        } elseif($paginationData['next_page_url'] == null) {
            $next = null;
        }

        if($paginationData['current_page']) {
            if(isset($newQueryString)) {
                $self = $request->url().'/?page='.$paginationData['current_page'].'&'.$newQueryString;
            } else {
                $self = $request->Url().'/?page='.$paginationData['current_page'].'&'.$queryString;
            }            
        }

        /**
         * With category, tags, ingredients
         */
        if(isset($with)) {
            if(in_array('category', $with)) {
                $dataCategories = $this->withCategory($langId);
            }

            if(in_array('tags', $with)) {
                $dataTags = $this->withTags($langId);
            }

            if(in_array('ingredients', $with)) {
                $dataIngredients = $this->withIngredients($langId);
            }
        }

        /**
         * Preparing data for final JSON response
         */
        $dataForJSON = [];
        foreach($meals as $meal) {
            /**
             * Meal data
             */
            $data = [
                'id' => $meal['id'],
                'title' => $meal['title'],
                'description' => $meal['description'],
                'status' => $meal['status']
            ];

            /**
             * Category data
             */
            if(isset($dataCategories)) {
                foreach($dataCategories as $cat) {
                    if($cat['id'] == $meal['category_id']) {
                        $data['category'] = [
                            'id' => $cat['id'],
                            'title' => $cat['title'],
                            'slug' => $cat['slug']
                        ];
                    }
                }
            }

            /**
             * Tags data
             */
            if(isset($dataTags)) {
                $this->meal_tags = $meal['tags_id'];
                $temp = array_where($dataTags, function($value, $key) {
                    if(in_array($value['id'], $this->meal_tags)) {
                        return $value;
                    }
                });

                $data['tags'] = [];
                foreach($temp as $item) {
                    array_push($data['tags'], collect($item));
                }
                unset($temp);
            }

            /**
             * Ingredients data
             */
            if(isset($dataIngredients)) {
                $this->meal_ingredients = $meal['ingredients_id'];
                $temp = array_where($dataIngredients, function($value, $key) {
                    if(in_array($value['id'], $this->meal_ingredients)) {
                        return $value;
                    }
                });

                $data['ingredients'] = [];
                foreach($temp as $item) {
                    array_push($data['ingredients'], collect($item));
                }
                unset($temp);
            }

            array_push($dataForJSON, $data);
        }

        /**
         * Final JSON response data
         */
        if(empty($dataForJSON)) {
            return response()->json([
                'message' => 'There are no meals for this query'
            ], 200);
        } else {
            return response()->json([
                'meta' => [
                    'currentPage'   => $currentPage,
                    'totalItems'    => $totalItems,
                    'itemsPerPage'  => $itemsPerPage,
                    'totalPages'    => $totalPages
                ],
                'data' => $dataForJSON,
                'links' => [
                    'prev' => $prev,
                    'next' => $next,
                    'self' => $self
                ]
            ], 200, [], JSON_UNESCAPED_SLASHES);
        }
    }

    /**
     * @param null $diffTime
     * @return array
     */
    public function diffTimeFiltering($diffTime = null)
    {
        if(isset($diffTime)) {
            $date = date('Y-m-d H:i:s', $diffTime);

            $mealsQuery = DB::table('meals')
                    ->join('mealtranslations', 'meals.meal_id', '=', 'mealtranslations.meal_id')
                    ->join('tags', 'meals.meal_id', '=', 'tags.meal_id')
                    ->join('ingredients', 'meals.meal_id', '=', 'ingredients.meal_id')
                    ->where('meals.created_at', '>', $date)
                    ->orWhere('meals.updated_at', '>', $date)
                    ->orWhere('meals.deleted_at', '>', $date)
                    ->select('meals.*', 'mealtranslations.*', 'tags.*', 'ingredients.*')
                    ->get();
        } else {
            $mealsQuery = DB::table('meals')
                    ->join('mealtranslations', 'meals.meal_id', '=', 'mealtranslations.meal_id')
                    ->join('tags', 'meals.meal_id', '=', 'tags.meal_id')
                    ->join('ingredients', 'meals.meal_id', '=', 'ingredients.meal_id')
                    ->whereNull('meals.deleted_at')
                    ->select('meals.*', 'mealtranslations.*', 'tags.*', 'ingredients.*')
                    ->get();
        }
        return $mealsQuery->toArray();
    }

    /**
     * @param $mealsQuery
     * @param $langId
     * @return array
     */
    public function sortedListOfAllMeals($mealsQuery, $langId)
    {
        $newMealsArray = [];
        foreach($mealsQuery as $meal) {
            if($meal->language_id == $langId) {
                array_push($newMealsArray, $meal->meal_id);
            }
        }
        $newMealsArray = array_unique($newMealsArray);
        $newMealsArray = array_flatten(array_sort($newMealsArray));
        return $newMealsArray;
    }

    /**
     * @param $mealsList
     * @param $mealsQuery
     * @param $langId
     * @param $date
     * @return array
     */
    public function filteringMealsByLanguage($mealsList, $mealsQuery, $langId, $date)
    {
        $temp1 = [];
        $temp2 = [];
        $tagIds = [];
        $ingredientIds = [];
        $meals = [];
        foreach($mealsList as $item) {
            foreach($mealsQuery as $meal) {
                if($meal->meal_id == $item && $meal->language_id == $langId) {
                    $meal->created_at && $meal->created_at > $date ? $mealStatus = 'created' : '';
                    $meal->updated_at && $meal->updated_at > $date ? $mealStatus = 'modified' : '';
                    $meal->deleted_at && $meal->deleted_at > $date ? $mealStatus = 'deleted' : '';
                    $data = [
                        'id' => $meal->meal_id,
                        'title' => $meal->title,
                        'description' => $meal->description,
                        'status' => $mealStatus,
                        'category_id' => $meal->category_id
                    ];
                    array_push($temp1, $meal->tag_id);
                    array_push($temp2, $meal->ingredient_id);
                }
            }

            $temp1 = array_sort(array_flatten($temp1));
            $temp2 = array_sort(array_flatten($temp2));
            array_push($tagIds, $temp1);
            array_push($ingredientIds, $temp2);

            $tagIds = array_unique($tagIds[0]);
            $tagIds = array_sort(array_flatten($tagIds));

            $ingredientIds = array_unique($ingredientIds[0]);
            $ingredientIds = array_sort(array_flatten($ingredientIds));

            $data['tags_id'] = $tagIds;
            $data['ingredients_id'] = $ingredientIds;

            array_push($meals, $data);

            $temp1 = [];
            $temp2 = [];
            $tagIds = [];
            $ingredientIds = [];
        }
        return $meals;
    }

    /**
     * @param $meals
     * @param $category
     * @return array
     */
    public function filteringMealsByCategory($meals, $category)
    {
        $newMealsArray = [];
        if($category != 'all') {
            foreach($meals as $meal) {
                if($category == 'null') {
                    if($meal['category_id'] == null) {
                        array_push($newMealsArray, $meal);
                    }
                } elseif($category == '!null') {
                    if($meal['category_id'] != null) {
                        array_push($newMealsArray, $meal);
                    }
                } else {
                    if($meal['category_id'] == $category) {
                        array_push($newMealsArray, $meal);
                    }
                }
            }
        } else {
            $newMealsArray = $meals;
        }
        return $newMealsArray;
    }

    /**
     * @param $meals
     * @param $tags
     * @return array
     */
    public function filteringMealsByTags($meals, $tags)
    {
        $newMealsArray = [];
        $tags = array_sort($tags);
        foreach($meals as $meal) {
            if(count(array_intersect($meal['tags_id'], $tags)) == count($tags)) {
                array_push($newMealsArray, $meal);
            }
        }
        return $newMealsArray;
    }

    /**
     * @param $meals
     * @param int $perPage
     * @param null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public function paginateMeals($meals, $perPage = 15, $page = null)
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $meals instanceof Collection ? $meals : Collection::make($meals);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page);
    }

    /**
     * @param $langId
     * @return array
     */
    public function withCategory($langId)
    {
        $categories = DB::table('categories')
            ->join('cattranslations', 'categories.category_id', '=', 'cattranslations.category_id')
            ->where('language_id', $langId)
            ->select('categories.*', 'cattranslations.*')
            ->get();

        $dataCategories = [];
        foreach($categories as $cat) {
            $temp = [
                'id' => $cat->category_id,
                'title' => $cat->title,
                'slug' => $cat->slug
            ];
            array_push($dataCategories, $temp);
        }
        return $dataCategories;
    }

    /**
     * @param $langId
     * @return array
     */
    public function withTags($langId)
    {
        $tags = DB::table('tags')
            ->join('tagtranslations', 'tags.tag_id', '=', 'tagtranslations.tag_id')
            ->where('language_id', $langId)
            ->select('tags.*', 'tagtranslations.*')
            ->get();

        $dataTags = [];
        foreach($tags as $tag) {
            $temp = [
                'id' => $tag->tag_id,
                'title' => $tag->title,
                'slug' => $tag->slug
            ];
            array_push($dataTags, $temp);
        }
        return $dataTags;
    }

    /**
     * @param $langId
     * @return array
     */
    public function withIngredients($langId)
    {
        $ingredients = DB::table('ingredients')
            ->join('ingtranslations', 'ingredients.ingredient_id', '=', 'ingtranslations.ingredient_id')
            ->where('language_id', $langId)
            ->select('ingredients.*', 'ingtranslations.*')
            ->get();

        $dataIngredients = [];
        foreach($ingredients as $ingredient) {
            $temp = [
                'id' => $ingredient->ingredient_id,
                'title' => $ingredient->title,
                'slug' => $ingredient->slug
            ];
            array_push($dataIngredients, $temp);
        }
        return $dataIngredients;
    }
}