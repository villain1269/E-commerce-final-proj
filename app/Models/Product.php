<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id', 'sku', 'name', 'category_id',
        'price', 'unit', 'rating', 'reviews',
        'wholesale', 'featured',
        'suited', 'tags', 'desc', 'specs',
    ];

    protected $casts = [
        'suited'    => 'array',
        'tags'      => 'array',
        'specs'     => 'array',
        'wholesale' => 'boolean',
        'featured'  => 'boolean',
        'rating'    => 'integer',
        'reviews'   => 'integer',
        'price'     => 'integer',
    ];

    /* ---------- Relationships ---------- */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /* ---------- Scopes ---------- */
    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('featured', true);
    }

    /* ---------- Helpers ---------- */
    public function hasTag(string $tag): bool
    {
        return in_array($tag, $this->tags ?? [], true);
    }

    public function isSuitedFor(string $key): bool
    {
        return in_array($key, $this->suited ?? [], true);
    }

    /**
     * Render a star string like ★★★★★ for the rating.
     */
    public function starString(): string
    {
        $r = max(0, min(5, (int) $this->rating));
        return str_repeat('★', $r) . str_repeat('☆', 5 - $r);
    }

    /**
     * Render price with peso sign, e.g. ₱450
     */
    public function peso(?int $multiplier = null): string
    {
        $amount = $multiplier ? $this->price * $multiplier : $this->price;
        return '₱' . number_format($amount);
    }

    /**
     * URL to the product's main image, if one exists on disk.
     *
     * Looks for the first matching file in storage/app/public/products/
     * using these naming conventions (in order):
     *   1. {public_id}.{ext}        e.g. p1.jpg, p1.png, p1.webp
     *   2. {sku}.{ext}              e.g. DG-CLS-10.jpg
     *   3. {slug}.{ext}             e.g. classic-pizza-dough-balls.jpg
     *
     * Returns null if no image is found (the Blade view falls back to
     * the original [PRODUCT IMAGE] placeholder).
     *
     * IMPORTANT: run `php artisan storage:link` once so the public
     * symlink exists at public/storage → storage/app/public.
     */
    public function imageUrl(): ?string
    {
        // Try by public_id (p1, p2, ...) — most common
        $candidates = [
            $this->public_id,
            $this->sku,
            str($this->name)->slug(),
        ];

        foreach ($candidates as $base) {
            foreach (['jpg', 'jpeg', 'png', 'webp', 'avif'] as $ext) {
                $relative = "products/{$base}.{$ext}";
                if (Storage::disk('public')->exists($relative)) {
                    return Storage::disk('public')->url($relative);
                }
            }
        }

        return null;
    }

    /**
     * Related products (different category first, then same category, minus self).
     */
    public function related(int $limit = 4)
    {
        $others = self::where('id', '!=', $this->id)->get();
        $sorted = $others->reject(fn ($p) => $p->category_id === $this->category_id)
            ->concat($others->filter(fn ($p) => $p->category_id === $this->category_id));
        return $sorted->take($limit);
    }

    /**
     * The second spec row's first cell label (used by the original JS to show "Ingredients:" etc.)
     */
    public function specSecondaryLabel(): ?string
    {
        $spec = Arr::get($this->specs, '1.0');
        return $spec ? (string) $spec : null;
    }

    public function specSecondaryValue(): ?string
    {
        $val = Arr::get($this->specs, '1.1');
        return $val ? (string) $val : null;
    }
}

