<?php

namespace Day4\NovaForms\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class   Form extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['title','slug','excerpt','is_active','fields','terms','btn','msg','email'];
    protected $fillable = ['label'];
    protected $with = ['translations'];

    /**
     * Scope a query to only include active pages.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->whereHas('translations', function($q){
            $q->where([
                ['is_active', '=', 1],
                ['locale', '=', app()->getLocale()]
            ]);
        });
    }

    /**
     * All the entries for this form
     */
    public function entries() : HasMany
    {
        $this->hasMany(FormEntry::class);
    }
}
