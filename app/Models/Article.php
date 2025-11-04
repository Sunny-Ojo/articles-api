<?php

namespace App\Models;

use App\Models\Scopes\PublishedArticleScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function toSearchableArray(): array
    {
        $array = $this->toArray();

        if (!empty($this->published_at)) {
            $array['published_at'] = Carbon::parse($this->published_at)->toIso8601String();
        }

        return $array;
    }
    // public static function booted(): void
    // {
    //     static::addGlobalScope(new PublishedArticleScope());
    // }

    public function scopeDrafts($query)
    {
        return $query->whereNull('published_at');
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }
}
