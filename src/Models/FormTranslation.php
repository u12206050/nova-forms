<?php

namespace Day4\NovaForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Whitecube\NovaFlexibleContent\Value\FlexibleCast;

class FormTranslation extends Model
{
    protected $fillable = ['title','slug','excerpt','is_active','fields','terms','btn','msg','email'];

    protected $casts = [
        'is_active' => 'boolean',
        'fields' => FlexibleCast::class,
        'terms' => FlexibleCast::class
    ];

    protected static function booted()
    {
        static::creating(function ($form) {
            if (empty($form->slug)) {
                $form->slug = Str::slug($form->title);
            }
        });
    }
}
