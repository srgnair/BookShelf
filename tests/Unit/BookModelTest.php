<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $this->assertTrue($book->user->is($user));
    }

    public function test_book_has_many_reviews(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->for($book)->create();

        $this->assertTrue($book->reviews->contains($review));
    }

    public function test_book_belongs_to_many_genres(): void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre);

        $this->assertTrue($book->genres->contains($genre));
    }

    public function test_book_can_be_favorited_by_users(): void
    {
        $book = Book::factory()->create();
        $user = User::factory()->create();

        $book->favoritedByUsers()->attach($user->id);

        $this->assertTrue($book->favoritedByUsers->contains($user));
    }
}
