<?php

namespace App\Services;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Support\Str;

class ArticleService
{
    public function __construct(
        protected ArticleRepository $repository
    ) {}

    public function list()
    {
        return $this->repository->all();
    }

    public function show(string $slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function store(array $data): Article
    {
        $data['slug'] = Str::slug($data['title']);
        return $this->repository->create($data);
    }

    public function update(Article $article, array $data): Article
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        return $this->repository->update($article, $data);
    }

    public function delete(Article $article): bool
    {
        return $this->repository->delete($article);
    }
}
