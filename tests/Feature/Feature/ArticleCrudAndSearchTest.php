<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleCrudAndSearchTest extends TestCase
{
    use RefreshDatabase;

    protected string $baseUrl = '/api/articles';

    protected function setUp(): void
    {
        parent::setUp();
        config(['scout.queue' => false]);
    }

    /** @test */
    public function it_returns_only_published_articles()
    {
        Article::factory()->count(3)->published()->create();
        Article::factory()->count(2)->unpublished()->create();

        $response = $this->getJson($this->baseUrl);

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_show_a_single_published_article_by_slug()
    {
        $article = Article::factory()->published()->create([
            'title' => 'Why Developers Love Open Source',
            'slug' => 'why-developers-love-open-source',
        ]);

        $response = $this->getJson("{$this->baseUrl}/{$article->slug}");

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Why Developers Love Open Source']);
    }

    /** @test */
    public function it_can_create_a_new_article()
    {
        $payload = $this->articlePayload();

        $response = $this->postJson($this->baseUrl, $payload);

        $response->assertCreated()
            ->assertJsonFragment(['title' => $payload['title']]);

        $this->assertDatabaseHas('articles', ['title' => $payload['title']]);
    }

    /** @test */
    public function it_can_update_an_article_and_refresh_slug_when_title_changes()
    {
        $article = Article::factory()->published()->create([
            'title' => 'Laravel Tips Every Beginner Should Know',
        ]);

        $response = $this->putJson("{$this->baseUrl}/{$article->id}", [
            'title' => 'Laravel Tips Every Developer Should Know',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Laravel Tips Every Developer Should Know']);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'slug' => 'laravel-tips-every-developer-should-know',
        ]);
    }

    /** @test */
    public function it_can_delete_an_article()
    {
        $article = Article::factory()->published()->create();

        $this->deleteJson("{$this->baseUrl}/{$article->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    /** @test */
    public function it_returns_only_published_articles_that_match_the_search_query()
    {
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

        $response = $this->getJson("{$this->baseUrl}/search?q=AI");

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Exploring the Future of AI in Education'])
            ->assertJsonMissing(['title' => 'AI Research Behind Closed Doors']);
    }

    /**
     * Generate a default article payload.
     */
    protected function articlePayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'How to Stay Productive as a Remote Developer',
            'content' => 'Working remotely can be challenging, but with the right routines and tools, it becomes enjoyable.',
            'published_at' => now(),
        ], $overrides);
    }
}
