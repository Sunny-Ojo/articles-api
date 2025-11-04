<?php

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['scout.queue' => false]);
});

test('returns only published articles', function () {
    Article::factory()->count(3)->published()->create();
    Article::factory()->count(2)->unpublished()->create();

    $response = $this->getJson('/api/articles');

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

test('can show a single published article by slug', function () {
    $article = Article::factory()->published()->create([
        'title' => 'Why Developers Love Open Source',
        'slug' => 'why-developers-love-open-source',
    ]);

    $response = $this->getJson("/api/articles/{$article->slug}");

    $response->assertOk()
        ->assertJsonFragment(['title' => 'Why Developers Love Open Source']);
});

test('can create a new article', function () {
    $payload = articlePayload();

    $response = $this->postJson('/api/articles', $payload);

    $response->assertCreated()
        ->assertJsonFragment(['title' => $payload['title']]);

    $this->assertDatabaseHas('articles', ['title' => $payload['title']]);
});

test('can update an article and refresh slug when title changes', function () {
    $article = Article::factory()->published()->create([
        'title' => 'Laravel Tips Every Beginner Should Know',
    ]);

    $response = $this->putJson("/api/articles/{$article->id}", [
        'title' => 'Laravel Tips Every Developer Should Know',
    ]);

    $response->assertOk()
        ->assertJsonFragment(['title' => 'Laravel Tips Every Developer Should Know']);

    $this->assertDatabaseHas('articles', [
        'id' => $article->id,
        'slug' => 'laravel-tips-every-developer-should-know',
    ]);
});

test('can delete an article', function () {
    $article = Article::factory()->published()->create();

    $this->deleteJson("/api/articles/{$article->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('articles', ['id' => $article->id]);
});

test('returns only published articles that match the search query', function () {
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

    sleep(1);

    $response = $this->getJson('/api/articles?search=AI');

    $response->assertOk()
        ->assertJsonFragment(['title' => 'Exploring the Future of AI in Education'])
        ->assertJsonMissing(['title' => 'AI Research Behind Closed Doors']);
});

function articlePayload(array $overrides = []): array
{
    return array_merge([
        'title' => 'How to Stay Productive as a Remote Developer',
        'content' => 'Working remotely can be challenging, but with the right routines and tools, it becomes enjoyable.',
        'is_published' => true,
        'author' => 'Sunny Ojo',
    ], $overrides);
}
