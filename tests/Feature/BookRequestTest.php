<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_title_is_required(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = $this->validBookData($genre->id);

        $data['title'] = '';

        $response = $this->actingAs($user)->post(route('books.store'), $data);

        $response->assertSessionHasErrors('title');
    }

    public function test_author_is_required(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = $this->validBookData($genre->id);

        $data['author'] = '';

        $response = $this->actingAs($user)->post(route('books.store'), $data);

        $response->assertSessionHasErrors('author');
    }

    public function test_isbn_must_be_13_digits(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = $this->validBookData($genre->id);

        $data['isbn'] = '123456789';

        $response = $this->actingAs($user)->post(route('books.store'), $data);

        $response->assertSessionHasErrors('isbn');
    }

    public function test_genres_are_required(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = $this->validBookData($genre->id);
        $data['genres'] = [];

        $response = $this->actingAs($user)->post(route('books.store'), $data);

        $response->assertSessionHasErrors('genres');
    }

    private function validBookData(int $genreId): array
    {
        return [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'genres' => [$genreId],
        ];
    }
}
