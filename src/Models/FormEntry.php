<?php

namespace Day4\NovaForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormEntry extends Model
{
    protected $fillable = ['form_id','locale','fields','terms','read'];
    protected $with = ['form'];

    protected $casts = [
        'fields' => 'object',
        'terms' => 'object'
    ];

    /**
     * The form this entry belongs to
     */
    public function form() : BelongsTo
    {
        return $this->belongsTo(Form::class);
    }
}
