<?php

namespace App\Http\Controllers;

use App\Models\HotelImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class GalleryController extends Controller
{
    public function index()
    {
        $images = HotelImage::active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($image) {
                $image->full_image_path = $image->full_image_path;
                return $image;
            })
            ->groupBy('category');



        return Inertia::render('Gallery', [
            'images' => $images,
        ]);
    }

    public function adminIndex()
    {
        $images = HotelImage::orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Add full_image_path to each image
        $images->getCollection()->transform(function ($image) {
            $image->full_image_path = $image->full_image_path;
            return $image;
        });

        $categories = [
            HotelImage::CATEGORY_ROOMS => 'Rooms',
            HotelImage::CATEGORY_FACILITIES => 'Facilities',
            HotelImage::CATEGORY_ATTRACTIONS => 'Attractions',
            HotelImage::CATEGORY_GENERAL => 'General',
        ];

        return Inertia::render('Admin/GalleryManagement', [
            'images' => $images,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Gallery store method called', [
            'request_data' => $request->all(),
            'files' => $request->hasFile('image') ? 'Image file present' : 'No image file',
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'category' => 'required|in:' . implode(',', [
                    HotelImage::CATEGORY_ROOMS,
                    HotelImage::CATEGORY_FACILITIES,
                    HotelImage::CATEGORY_ATTRACTIONS,
                    HotelImage::CATEGORY_GENERAL,
                ]),
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
                'is_featured' => 'required|in:0,1,true,false',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            Log::info('Validation passed');

            $imagePath = $request->file('image')->store('hotel-images', 'public');
            Log::info('Image stored at: ' . $imagePath);

            $image = HotelImage::create([
                'title' => $request->title,
                'description' => $request->description,
                'image_path' => $imagePath,
                'category' => $request->category,
                'is_featured' => in_array($request->input('is_featured'), ['1', 'true', true]),
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => true,
            ]);

            Log::info('Image created successfully', ['image_id' => $image->id]);

            // Return JSON response for API calls
            if ($request->expectsJson() || ($request->header('X-Requested-With') === 'XMLHttpRequest' && !$request->header('X-Inertia'))) {
                $image->full_image_path = $image->full_image_path;
                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'image' => $image
                ], 201);
            }

            // For Inertia requests, redirect back with success message
            return redirect()->back()->with('success', 'Image uploaded successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()));
            
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Image upload failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Image upload failed: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        Log::info('Gallery update method called', [
            'request_data' => $request->all(),
            'is_featured_raw' => $request->input('is_featured'),
            'is_featured_type' => gettype($request->input('is_featured')),
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'category' => 'required|in:' . implode(',', [
                    HotelImage::CATEGORY_ROOMS,
                    HotelImage::CATEGORY_FACILITIES,
                    HotelImage::CATEGORY_ATTRACTIONS,
                    HotelImage::CATEGORY_GENERAL,
                ]),
                'is_featured' => 'required|in:0,1,true,false',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'required|in:0,1,true,false',
            ]);

            Log::info('Validation passed for update');
            Log::info('Boolean conversion test', [
                'is_featured_raw' => $request->input('is_featured'),
                'is_featured_converted' => in_array($request->input('is_featured'), ['1', 'true', true]),
                'is_active_raw' => $request->input('is_active'),
                'is_active_converted' => in_array($request->input('is_active'), ['1', 'true', true]),
            ]);

            $image = HotelImage::findOrFail($id);
            
            $image->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'is_featured' => in_array($request->input('is_featured'), ['1', 'true', true]),
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => in_array($request->input('is_active'), ['1', 'true', true]),
            ]);

            // Return JSON response for API calls
            if ($request->expectsJson() || ($request->header('X-Requested-With') === 'XMLHttpRequest' && !$request->header('X-Inertia'))) {
                $freshImage = $image->fresh();
                $freshImage->full_image_path = $freshImage->full_image_path;
                return response()->json([
                    'success' => true,
                    'message' => 'Image updated successfully',
                    'image' => $freshImage
                ]);
            }

            // For Inertia requests, redirect back with success message
            return redirect()->back()->with('success', 'Image updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed: ' . json_encode($e->errors()));
            
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Image update failed: ' . $e->getMessage());
            
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Image update failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Image update failed']);
        }
    }

    public function destroy($id)
    {
        try {
            $image = HotelImage::findOrFail($id);
            
            // Delete the physical file
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            
            $image->delete();

            // Return JSON response for API calls
            if (request()->expectsJson() || (request()->header('X-Requested-With') === 'XMLHttpRequest' && !request()->header('X-Inertia'))) {
                return response()->json([
                    'success' => true,
                    'message' => 'Image deleted successfully'
                ]);
            }

            // For Inertia requests, redirect back with success message
            return redirect()->back()->with('success', 'Image deleted successfully');

        } catch (\Exception $e) {
            Log::error('Image deletion failed: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Image deletion failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Image deletion failed']);
        }
    }

    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:hotel_images,id',
            'images.*.sort_order' => 'required|integer|min:0',
        ]);

        try {
            foreach ($request->images as $imageData) {
                HotelImage::where('id', $imageData['id'])
                    ->update(['sort_order' => $imageData['sort_order']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Sort order updated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('Sort order update failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Sort order update failed'
            ], 500);
        }
    }

    public function toggleFeatured($id)
    {
        try {
            $image = HotelImage::findOrFail($id);
            $image->update(['is_featured' => !$image->is_featured]);

            // Return JSON response for API calls
            if (request()->expectsJson() || (request()->header('X-Requested-With') === 'XMLHttpRequest' && !request()->header('X-Inertia'))) {
                $freshImage = $image->fresh();
                $freshImage->full_image_path = $freshImage->full_image_path;
                return response()->json([
                    'success' => true,
                    'message' => 'Featured status updated successfully',
                    'image' => $freshImage
                ]);
            }

            // For Inertia requests, redirect back with success message
            return redirect()->back()->with('success', 'Featured status updated successfully');

        } catch (\Exception $e) {
            Log::error('Featured status update failed: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Featured status update failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Featured status update failed']);
        }
    }

    public function toggleActive($id)
    {
        try {
            $image = HotelImage::findOrFail($id);
            $image->update(['is_active' => !$image->is_active]);

            // Return JSON response for API calls
            if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                $freshImage = $image->fresh();
                $freshImage->full_image_path = $freshImage->full_image_path;
                return response()->json([
                    'success' => true,
                    'message' => 'Active status updated successfully',
                    'image' => $freshImage
                ]);
            }

            return redirect()->back()->with('success', 'Active status updated successfully');

        } catch (\Exception $e) {
            Log::error('Active status update failed: ' . $e->getMessage());
            
            if (request()->expectsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Active status update failed: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Active status update failed']);
        }
    }

    public function getImagesByCategory($category)
    {
        try {
            $images = HotelImage::active()
                ->byCategory($category)
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(function ($image) {
                    $image->full_image_path = $image->full_image_path;
                    return $image;
                });

            return response()->json([
                'success' => true,
                'images' => $images,
            ]);

        } catch (\Exception $e) {
            Log::error('Category images retrieval failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Images retrieval failed'
            ], 500);
        }
    }

    public function getFeaturedImages()
    {
        try {
            $images = HotelImage::active()
                ->featured()
                ->orderBy('sort_order', 'asc')
                ->get()
                ->map(function ($image) {
                    $image->full_image_path = $image->full_image_path;
                    return $image;
                });

            return response()->json([
                'success' => true,
                'images' => $images,
            ]);

        } catch (\Exception $e) {
            Log::error('Featured images retrieval failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Featured images retrieval failed'
            ], 500);
        }
    }
}
