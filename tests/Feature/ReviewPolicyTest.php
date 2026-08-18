<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_owner_can_update_review(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $review = Review::factory()
            ->for($owner)
            ->create();

        $this->assertTrue($owner->can('update', $review));
        $this->assertFalse($otherUser->can('update', $review));
    }

    public function test_only_owner_can_delete_review(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $review = Review::factory()
            ->for($owner)
            ->create();

        $this->assertTrue($owner->can('delete', $review));
        $this->assertFalse($otherUser->can('delete', $review));
    }
}
