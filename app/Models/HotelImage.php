<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HotelImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'image_path',
        'category',
        'is_featured',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Category constants
    const CATEGORY_ROOMS = 'rooms';
    const CATEGORY_FACILITIES = 'facilities';
    const CATEGORY_ATTRACTIONS = 'attractions';
    const CATEGORY_GENERAL = 'general';

    public function getFullImagePathAttribute()
    {
        // Check if the image path exists and is not empty
        if (!empty($this->image_path) && Storage::disk('public')->exists($this->image_path)) {
            try {
                return asset('storage/' . $this->image_path);
            } catch (\Exception $e) {
                // If asset() fails, return the direct path
                return url('storage/' . $this->image_path);
            }
        }
        
        // Return placeholder images based on category
        $placeholders = [
            self::CATEGORY_ROOMS => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?w=800&h=600&fit=crop',
            self::CATEGORY_FACILITIES => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop',
            self::CATEGORY_ATTRACTIONS => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
            self::CATEGORY_GENERAL => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop',
        ];
        
        return $placeholders[$this->category] ?? $placeholders[self::CATEGORY_GENERAL];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function imageExists()
    {
        return !empty($this->image_path) && Storage::disk('public')->exists($this->image_path);
    }

    public function getImageUrlAttribute()
    {
        return $this->full_image_path;
    }
}
