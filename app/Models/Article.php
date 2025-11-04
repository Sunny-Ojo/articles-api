<?php

namespace App\Models;

use App\Models\Scopes\PublishedArticleScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Article extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'author' => $this->author,
            'published_at' => $this->published_at,
        ];
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
