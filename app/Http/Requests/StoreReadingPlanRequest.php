<?php

namespace App\Http\Requests;

use App\Enums\ReadingPlanStatus;
use App\Models\ReadingPlan;
use Closure;
use Illuminate\Foundation\Http\FormRequest;

class StoreReadingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'integer',
                'exists:books,id',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $exists = ReadingPlan::where('user_id', $this->user()->id)
                        ->where('book_id', $value)
                        ->where('status', [ReadingPlanStatus::InProgress,
                            ReadingPlanStatus::Expired, ])
                        ->exists();

                    if ($exists) {
                        $fail('この書籍は既に進行中の読書計画が存在します。');
                    }
                },
            ],
            'target_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'book_id' => '書籍',
            'target_date' => '期日',
        ];
    }
}
