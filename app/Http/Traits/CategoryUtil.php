<?php

namespace App\Http\Traits;

trait CategoryUtil
{
    public static function generateParentCategories(&$categories)
    {
        $parent_categories  = [];
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $parent_categories[$category->id] = $category->name;
            }
        }

        return $parent_categories;
    }
}
