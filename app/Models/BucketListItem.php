<?php

namespace App\Models;

use Database\Factories\BucketListItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'category'])]
class BucketListItem extends Model
{
    /** @use HasFactory<BucketListItemFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['is_fulfilled' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
