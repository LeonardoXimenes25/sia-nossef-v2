<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsCategory extends Model
{
    protected $table = 'news_categories';
    public $timestamps = false;
    
    protected $fillable = [
        'name', 
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug saat create dan update
        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    public function news()
    {
        return $this->hasMany(News::class, 'category_id');
    }
}
