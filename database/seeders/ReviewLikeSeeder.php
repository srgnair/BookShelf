<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $reviews = Review::all();

        foreach ($reviews as $review) {
            $likeCount = rand(0, 3);

            $likeUsers = $users
                ->where('id', '!=', $review->user_id)
                ->random(min($likeCount, $users->count() - 1));

            foreach ($likeUsers as $user) {
                $user->likedReviews()
                    ->syncWithoutDetaching([$review->id]);
            }
        }
    }
}
