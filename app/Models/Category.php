<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $guarded = [];

    protected $casts = [
        'id' => 'int',
    ];

    protected static function booted()
    {
        static::saved(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });
        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::flush();
        });
    }

    /**
     * Parent category (for sub-categories).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Child categories (sub-categories).
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('name');
    }

    /**
     * Company that owns this category.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Standard sizes for this category.
     */
    public function sizes(): HasMany
    {
        return $this->hasMany(CategorySize::class)
            ->orderBy('height_in')
            ->orderBy('length_in')
            ->orderBy('width_in')
            ->orderBy('display_order');
    }

    /**
     * Copy sizes from another category into this one.
     *
     * Returns the number of sizes added.
     */
    public function copySizesFrom(Category $source): int
    {
        if ($source->id === $this->id) {
            return 0;
        }

        $added = 0;
        foreach ($source->sizes as $size) {
            $exists = $this->sizes()
                ->where('length_in', $size->length_in)
                ->where('width_in', $size->width_in)
                ->where('height_in', $size->height_in)
                ->exists();

            if ($exists) {
                continue;
            }

            $this->sizes()->create([
                'length_in' => $size->length_in,
                'width_in' => $size->width_in,
                'height_in' => $size->height_in,
            ]);
            $added++;
        }

        return $added;
    }

    /**
     * Get all sub-categories recursively.
     */
    public function allChildren(): array
    {
        $children = $this->children;
        $all = $children->all();
        foreach ($children as $child) {
            $all = array_merge($all, $child->allChildren());
        }
        return array_values($all);
    }

    /**
     * Check if this category or any parent is 'Lamination'.
     */
    public function isLamination(): bool
    {
        if (strcasecmp($this->name, 'Lamination') === 0) {
            return true;
        }
        if ($this->parent) {
            return $this->parent->isLamination();
        }
        return false;
    }
}
