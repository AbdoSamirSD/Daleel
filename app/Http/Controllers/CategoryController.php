<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    //

    public function showAll(Request $request)
    {

        $categories = Category::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $categories->where('name', 'like', '%' . $searchTerm . '%');
        }

        $categories = $categories->get();
        return response()->json([
            'categories' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'icon' => $category->icon ? asset('storage/' . $category->icon) : null,
                ];
            })
        ]);
    }
    public function index(Category $category)
    {
        // Logic to retrieve and return one category
        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'icon' => $category->icon ? asset('storage/' . $category->icon) : null,
        ]);
    }


    public function store(Request $request)
    {
        // Logic to create a new category

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'icon' => 'required|image|mimes:png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Category::create([
            'name' => request('name'),
            'icon' => request()->file('icon')->store('category_icons', 'public'),
        ]);

        return response()->json(['message' => 'Category created successfully']);
    }

    public function update(Category $category)
    {
        // Logic to update an existing category

        $validator = Validator::make(request()->all(), [
            'name' => 'sometimes|string|max:255',
            'icon' => 'sometimes|image|mimes:png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->update([
            'name' => request('name', $category->name),
            'icon' => request()->file('icon') ? request()->file('icon')->store('category_icons', 'public') : $category->icon,
        ]);

        return response()->json(['message' => 'Category updated successfully']);
    }

    public function destroy(Category $category)
    {
        // Logic to delete a category

        // delete icon file if exists
        if ($category->icon && Storage::disk('public')->exists($category->icon)) {
            Storage::disk('public')->delete($category->icon);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
