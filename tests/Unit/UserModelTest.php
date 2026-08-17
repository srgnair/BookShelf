<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_many_books(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->for($user)->create();

        $this->assertTrue($user->books->contains($book));
    }

    public function test_user_has_many_reviews(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->for($user)->create();

        $this->assertTrue($user->reviews->contains($review));
    }

    public function test_user_can_have_favorite_books(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book->id);

        $this->assertTrue($user->favoriteBooks->contains($book));
    }

    public function test_user_can_have_liked_reviews(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $user->likedReviews()->attach($review->id);

        $this->assertTrue($user->likedReviews->contains($review));
    }
}
