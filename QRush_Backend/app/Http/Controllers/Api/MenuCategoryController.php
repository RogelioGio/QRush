<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuCategoryRequest;
use App\Models\MenuCategory;
use Illuminate\Http\Request;

class MenuCategoryController extends Controller
{
    public function index()
    {
        // Logic to list menu categories
        $categories = MenuCategory::orderBy('name')->get();
        return response()->json($categories);
    }

    public function store(MenuCategoryRequest $request)
    {
        // Logic to create a new menu category
        $validated = $request->validated();

        $category = MenuCategory::create([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json($category, 201);
    }

    public function show($id)
    {
        // Logic to show a specific menu category
        $category = MenuCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(MenuCategoryRequest $request, MenuCategory $menu_category)
    {
        // Logic to update a specific menu category
        $validated = $request->validated();

        $menu_category->update($validated);

        return response()->json($menu_category);
    }

    public function destroy(MenuCategory $menu_category)
    {
        // Logic to delete a specific menu category
        $menu_category->update(['is_active' => false]);

        return response()->json([
            'message' => 'Menu category deactivated successfully',
            'data' => $menu_category
        ]);
    }

    public function deactiveate(MenuCategory $menu_category)
    {
        // // Logic to deactivate a specific menu category
        // $menu_category->update(['is_active' => false]);

        // return response()->json([
        //     'message' => 'Menu category deactivated successfully',
        //     'data' => $menu_category
        // ]);
    }

    public function activate(MenuCategory $menu_category)
    {
        // Logic to activate a specific menu category
        $menu_category->update(['is_active' => true]);

        return response()->json([
            'message' => 'Menu category activated successfully',
            'data' => $menu_category
        ]);
    }

    public function indexActiveCategories()
    {
        // Logic to list only active menu categories
        $categories = MenuCategory::where('is_active', true)->orderBy('name')->get();
        return response()->json($categories);
    }
}
