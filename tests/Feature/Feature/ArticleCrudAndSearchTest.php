<?php

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['scout.queue' => false]); // disable queue for tests
    $this->baseUrl = '/api/articles';
});

/** 
 * Returns only published articles 
 */
it('returns only published articles', function () {
    Article::factory()->count(3)->published()->create();
    Article::factory()->count(2)->unpublished()->create();

    $response = getJson($this->baseUrl);

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

/**
 * Shows a single published article by slug
 */
it('can show a single published article by slug', function () {
    $article = Article::factory()->published()->create([
        'title' => 'Why Developers Love Open Source',
        'slug' => 'why-developers-love-open-source',
    ]);

    $response = getJson("{$this->baseUrl}/{$article->slug}");

    $response->assertOk()
        ->assertJsonFragment(['title' => 'Why Developers Love Open Source']);
});

/**
 * Creates a new article
 */
it('can create a new article', function () {
    $payload = articlePayload();

    $response = postJson($this->baseUrl, $payload);

    $response->assertCreated()
        ->assertJsonFragment(['title' => $payload['title']]);

    $this->assertDatabaseHas('articles', ['title' => $payload['title']]);
});

/**
 * Updates an article and refreshes slug when title changes
 */
it('can update an article and refresh slug when title changes', function () {
    $article = Article::factory()->published()->create([
        'title' => 'Laravel Tips Every Beginner Should Know',
    ]);

    $response = putJson("{$this->baseUrl}/{$article->id}", [
        'title' => 'Laravel Tips Every Developer Should Know',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['title' => 'Laravel Tips Every Developer Should Know']);

    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        'slug' => 'laravel-tips-every-developer-should-know',
    ]);
});

/**
 * Deletes an article
 */
it('can delete an article', function () {
    $article = Article::factory()->published()->create();

    $response = deleteJson("{$this->baseUrl}/{$article->id}");

    $response->assertOk()
        ->assertJson([
            'data' => null,
            'message' => 'Article deleted successfully',
            'status' => true
        ]);

    $this->assertDatabaseMissing('articles', ['id' => $article->id]);
});

/**
 * Returns only published articles that match the search query
 */
it('returns only published articles that match the search query', function () {
    $published = Article::factory()->published()->create([
        'title' => 'Exploring the Future of AI in Education',
        'content' => 'AI is transforming how students learn and teachers teach.',
    ]);

    $unpublished = Article::factory()->unpublished()->create([
        'title' => 'AI Research Behind Closed Doors',
        'content' => 'Some research is still confidential and not for public view.',
    ]);

    $published->searchable();
    $unpublished->searchable();

    sleep(1); // wait for Elasticsearch indexing

    $response = getJson("{$this->baseUrl}/search?q=AI");

    $response->assertOk()
        ->assertJsonFragment(['title' => 'Exploring the Future of AI in Education'])
        ->assertJsonMissing(['title' => 'AI Research Behind Closed Doors']);
});

/**
 * Helper function to generate article payload
 */
function articlePayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'How to Stay Productive as a Remote Developer',
        'content' => 'Working remotely can be challenging, but with the right routines and tools, it becomes enjoyable.',
        'published_at' => now(),
    ], $overrides);
}
