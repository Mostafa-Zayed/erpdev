<?php

namespace App\Repositories;

use App\Interfaces\CategoryInterface;
use App\Category;
use App\Http\Traits\BusinessService;

class CategoryRepository implements CategoryInterface
{
    use BusinessService;

    public function getAll($businessId, $type, $parent = null)
    {

        $query = Category::Type($type);

        if (!is_null($parent) && $parent == 0) {

            $query = $query->OnlyParent();
        } elseif (!is_null($parent) && $parent == 1) {

            $query = $query->OnlyChild();
        }

        $query = $query->select(['name', 'short_code', 'description', 'id', 'parent_id']);

        $categories = $query->get();

        return !empty($categories) && $categories->count() > 0 ? $categories : collect([]);
    }

    public function getAllParent($businessId, $type)
    {
        return $this->getAll($businessId, $type, 0);
    }

    public function getAllChilde($businessId, $type)
    {
        return $this->getAll($businessId, $type, 1);
    }

    public function getChild($categoryId, $businessId = null)
    {
        $businessId = !empty($businessId) ? $businessId : $this->getBusinessId();

        return Category::whee('business_id', $businessId)->OnlyChild($categoryId);
    }

    public function generateCategoryDetails(& $request)
    {
        $input = $request->only(['name', 'short_code', 'category_type', 'description']);
        if (!empty($request->input('add_as_sub_cat')) &&  $request->input('add_as_sub_cat') == 1 && !empty($request->input('parent_id'))) {
            $input['parent_id'] = $request->input('parent_id');
        } else {
            $input['parent_id'] = 0;
        }
        $input['business_id'] = BusinessService::getBusinessId();
        $input['created_by'] = BusinessService::getUser();

        return $input;
    }
}
