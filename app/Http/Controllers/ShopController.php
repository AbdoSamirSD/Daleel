<?php

namespace App\Http\Controllers;
use App\Models\Shop;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use App\Models\Banner;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    //

    public function shopsByCategory($categoryId, Request $request)
    {
        // Logic to retrieve shops by category
        $category = Category::find($categoryId);
        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $query = Shop::where('category_id', $categoryId);

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('address', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%');
            });
        }

        // shops paginated
        $shops = $query->paginate(10);
        return response()->json([
            'shops' => $shops->map(function ($shop) {
                return [
                    'name' => $shop->name,
                    'image' => $shop->image ? asset('public/' . $shop->image) : null,
                    'description' => $shop->description,
                    'address' => $shop->address,
                    'phone' => $shop->phone,
                    'category_id' => $shop->category_id,
                ];
            }),
            'pagination' => [
                'total' => $shops->total(),
                'per_page' => $shops->perPage(),
                'current_page' => $shops->currentPage(),
                'last_page' => $shops->lastPage(),
                'from' => $shops->firstItem(),
                'to' => $shops->lastItem(),
            ]
        ]);
    }

    public function showDetails($shopId)
    {
        $shop = Shop::find($shopId);
        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }
        // Logic to show shop details
        return response()->json([
            'id' => $shop->id,
            'name' => $shop->name,
            'category_id' => $shop->category_id,
            'description' => $shop->description,
            'image' => $shop->image ? asset('public/' . $shop->image) : null,
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
            $image = $request->file('image');
            $image_name = time() . '_' . $request->file('image')->getClientOriginalName();
            $image->move(public_path('shop_images'), $image_name);
            $data['image'] = 'shop_images/' . $image_name;
        }
        $shop = Shop::create($data);
        return response()->json(['message' => 'Shop created successfully', 'shop' => [
            'name' => $shop->name,
            'image' => isset($data['image']) ? asset('public/' . $data['image']) : null,
            'category_id' => $shop->category_id,
            'address' => $shop->address,
            'phone' => $shop->phone,
        ]], 201);
    }

    public function update( $shopId)
    {
        $shop = Shop::find($shopId);
        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }
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
            if ($shop->image && file_exists(public_path($shop->image))) {
                unlink(public_path($shop->image));
            }

            $image = request()->file('image');
            $image_name = time() . '_' . request()->file('image')->getClientOriginalName();
            $image->move(public_path('shop_images'), $image_name);

            $data['image'] = 'shop_images/' . $image_name;
        }
        $shop->update([
            'name' => $data['name'] ?? $shop->name,
            'category_id' => $data['category_id'] ?? $shop->category_id,
            'description' => $data['description'] ?? $shop->description,
            'image' => $data['image'] ?? $shop->image,
            'address' => $data['address'] ?? $shop->address,
            'phone' => $data['phone'] ?? $shop->phone,
        ]);
        return response()->json(['message' => 'Shop updated successfully', 'shop' => [
            'name' => $shop->name,
        ]], 200);
    }

    public function destroy($shopId)
    {
        $shop = Shop::find($shopId);
        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }
        // Logic to delete a shop
        // delete image file if exists from public path
        if ($shop->image && file_exists(public_path($shop->image))) {
            unlink(public_path($shop->image));
        }
        $shop->delete();
        return response()->json(['message' => 'Shop deleted successfully'], 200);
    }

    public function listBanners()
    {
        // Logic to list all banners
        // Assuming Banner is another model related to Shop
        $banners = Banner::all();
        return response()->json([ 
            'banners' => $banners->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'image' => $banner->image_path ? asset('public/' . $banner->image_path) : null,
                ];
            })
        ], 200);
    }

    public function uploadBanner(Request $request)
    {
        // Logic to upload a new banner
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'image' => 'required|image|max:4096',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $banner = $request->file('image');
        $image_name = time() . '_' . $banner->getClientOriginalName();
        $banner->move(public_path('banners'), $image_name);
        $banner = Banner::create(['image_path' => 'banners/' . $image_name]);
        $title = $request->input('title');
        if ($title) {
            $banner->title = $title;
            $banner->save();
        }


        return response()->json(['message' => 'Banner uploaded successfully', 'banner' => $banner], 201);
    }

    public function deleteBanner($bannerId)
    {
        $banner = Banner::find($bannerId);
        if (!$banner) {
            return response()->json(['error' => 'Banner not found'], 404);
        }
        // Logic to delete a banner
        if (!empty($banner->image)) 
        {
            $imagePath = public_path($banner->image);

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        $banner->delete();
        return response()->json(['message' => 'Banner deleted successfully'], 200);
    }

    public function adminLogin(Request $request)
    {
        // Logic for admin login
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $validator->validated();

        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = auth()->user()->createToken('admin-token')->plainTextToken;

        return response()->json(['message' => 'Login successful', 'token' => $token], 200);
    }
}
