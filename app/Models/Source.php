<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'notebook_id',
        'uploaded_by',
        'type',
        'name',
        'original_name',
        'storage_disk',
        'storage_path',
        'source_url',
        'mime_type',
        'file_size',
        'status',
        'content_hash',
        'metadata',
        'summary',
        'extracted_text',
        'indexed_at',
        'last_processed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'indexed_at' => 'datetime',
            'last_processed_at' => 'datetime',
        ];
    }

    /**
     * Get the notebook that owns the source.
     */
    public function notebook(): BelongsTo
    {
        return $this->belongsTo(Notebook::class);
    }

    /**
     * Get the user who uploaded the source.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the embeddings for the source.
     */
    public function embeddings(): HasMany
    {
        return $this->hasMany(AiEmbedding::class);
    }

    /**
     * Get the activity logs for the source.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }
}
