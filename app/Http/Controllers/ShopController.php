<?php

namespace App\Http\Controllers;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Banner;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    //

    public function shopsByCategory($categoryId)
    {
        // Logic to retrieve shops by category
        $category = Category::find($categoryId);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }
        return response()->json([
            'shops' => $category->shops->map(function ($shop) {
                return [
                    'name' => $shop->name,
                    'image' => $shop->image && Storage::disk('public')->exists($shop->image) ? asset('storage/' . $shop->image) : null,
                ];
            })
        ]);
    }

    public function showDetails(Shop $shop)
    {
        // Logic to show shop details
        return response()->json([
            'id' => $shop->id,
            'name' => $shop->name,
            'category_id' => $shop->category_id,
            'description' => $shop->description,
            'image' => $shop->image && Storage::disk('public')->exists($shop->image) ? asset('storage/' . $shop->image) : null,
            'address' => $shop->address,
            'phone' => $shop->phone,
        ]);
    }


    public function store(Request $request)
    {
        // Logic to create a new shop
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:categories,id',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();
        if($request->hasFile('image')) {
            // Handle image upload
            $imagePath = $request->file('image')->store('shop_images', 'public');
            $data['image'] = $imagePath;
        }
        Shop::create($data);
        return response()->json(['message' => 'Shop created successfully', 'shop' => [
            'name' => $data['name'],
        ]], 201);
    }

    public function update(Shop $shop)
    {
        // Logic to update an existing shop
        $validator = Validator::make(request()->all(), [
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'sometimes|image|max:2048',
            'address' => 'sometimes|string|max:500',
            'phone' => 'sometimes|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = $validator->validated();

        if(request()->hasFile('image')) {
            // Handle image upload and update
            if ($shop->image && Storage::disk('public')->exists($shop->image)) {
                Storage::disk('public')->delete($shop->image);
            }
            $imagePath = request()->file('image')->store('shop_images', 'public');
            $data['image'] = $imagePath;
        }
        $shop->update($data);
        return response()->json(['message' => 'Shop updated successfully', 'shop' => [
            'name' => $shop->name,
        ]], 200);
    }

    public function destroy(Shop $shop)
    {
        // Logic to delete a shop
        // delete image file if exists
        if ($shop->image && Storage::disk('public')->exists($shop->image)) {
            Storage::disk('public')->delete($shop->image);
        }
        $shop->delete();
        return response()->json(['message' => 'Shop deleted successfully'], 200);
    }


    public function searchShops(Request $request)
    {
        if (!$request->filled('query')) {
            return response()->json(['error' => 'Search query is required'], 400);
        }
        $query = $request->input('query');
        $shops = Shop::select('id', 'name', 'image', 'description')
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->paginate(20);

        if ($shops->total() === 0) {
            return response()->json(['message' => 'No shops found matching the query'], 404);
        }

        $shops->getCollection()->transform(function ($shop) {
            return [
                'id' => $shop->id,
                'name' => $shop->name,
                'image' => $shop->image && Storage::disk('public')->exists($shop->image) ? asset('storage/' . $shop->image) : null,
            ];
        });

        return response()->json(['shops' => $shops,
            'meta' => [
                'current_page' => $shops->currentPage(),
                'last_page' => $shops->lastPage(),
                'per_page' => $shops->perPage(),
                'total' => $shops->total(),
            ]
        ], 200);
    }

    public function listBanners()
    {
        // Logic to list all banners
        // Assuming Banner is another model related to Shop
        $banners = Banner::all();
        return response()->json(['banners' => $banners], 200);
    }

    public function uploadBanner(Request $request)
    {
        // Logic to upload a new banner
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $imagePath = $request->file('image')->store('banners', 'public');
        $banner = Banner::create(['image' => $imagePath]);

        return response()->json(['message' => 'Banner uploaded successfully', 'banner' => $banner], 201);
    }

    public function deleteBanner(Banner $banner)
    {
        // Logic to delete a banner
        if (Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->delete();
        return response()->json(['message' => 'Banner deleted successfully'], 200);
    }

    public function showBanner(Banner $banner)
    {
        // Logic to show banner details
        return response()->json([
            'id' => $banner->id,
            'image' => $banner->image && Storage::disk('public')->exists($banner->image) ? asset('storage/' . $banner->image) : null,
        ]);
    }
}
