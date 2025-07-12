<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    public $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'category_slug',
        'category_image',
        'editable',
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function (Category $model) {
            if ($model->isDirty('category_name')) {
                $slug = Str::slug($model->category_name);
                $originalSlug = $slug;
                $count = 1;

                while (
                    static::where('category_slug', $slug)
                        ->where('category_id', '!=', $model->category_id)
                        ->withTrashed()
                        ->exists()
                ) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }

                $model->category_slug = $slug;
            }
        });
    }
}
