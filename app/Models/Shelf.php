<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Shelf extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'color',
        'order',
        'is_collapsed',
    ];

    protected $casts = [
        'is_collapsed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notebooks(): BelongsToMany
    {
        return $this->belongsToMany(Notebook::class, 'shelf_notebook')
            ->withPivot('order')
            ->orderBy('shelf_notebook.order');
    }
}
