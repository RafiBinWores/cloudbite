<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'is_slider',
        'start_at',
        'end_at',
        'item_type',
        'item_id',
        'status',
    ];

    public function scopeSearch($query, $value)
    {
        $query->where('title', 'like', "%{$value}%")->orWhere('status', 'like', "%{$value}%")->orWhere('created_at', 'like', "%{$value}%");
    }

    // For getting dis or category names
    public function getItemNameAttribute()
    {
        if (!$this->item_type || !$this->item_id) {
            return 'N/A';
        }

        try {
            if ($this->item_type === 'category') {
                $item = Category::find($this->item_id);
                return $item->name ?? 'Deleted Category';
            }

            if ($this->item_type === 'dish') {
                $item = Dish::find($this->item_id);
                return $item->title ?? 'Deleted Dish';
            }

            return 'Unknown Type';
        } catch (\Exception $e) {
            return 'Error Loading Item';
        }
    }

    // For dish Slug
    public function getItemSlugAttribute()
    {
        if (!$this->item_type || !$this->item_id) {
            return null;
        }

        try {
            if ($this->item_type === 'dish') {
                $item = \App\Models\Dish::find($this->item_id);
                return $item->slug ?? null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
