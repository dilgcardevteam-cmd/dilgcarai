<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotebookMember extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'notebook_id',
        'user_id',
        'invited_by',
        'permission',
        'can_share',
    ];

    /**
     * Get the notebook for the membership.
     */
    public function notebook(): BelongsTo
    {
        return $this->belongsTo(Notebook::class);
    }

    /**
     * Get the user for the membership.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the inviter for the membership.
     */
    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
