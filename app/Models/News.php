<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\NewsCategory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'content',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(NewsCategory::class, 'category_id');
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug saat create dan update
        static::creating(function ($news) {
            $news->slug = Str::slug($news->title);
        });

        static::updating(function ($news) {
            $news->slug = Str::slug($news->title);
        });
    }

}
