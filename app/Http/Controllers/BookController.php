<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $query = Book::with('genres');

        $query->when($request->input('keyword'), function ($q, $keyword): void {
            $q->where(function ($q) use ($keyword): void {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('author', 'like', "%{$keyword}%");
            });
        });

        $query->when($request->input('genre_id'), function ($q, $genreId): void {
            $q->whereHas('genres', function ($q) use ($genreId): void {
                $q->where('genres.id', $genreId);
            });
        });

        switch ($request->input('sort')) {
            case 'oldest':
                $query->oldest()
                    ->orderBy('id');
                break;

            case 'title':
                $query->orderBy('title')
                    ->orderBy('id');
                break;

            case 'rating':
                $query->withAvg('reviews', 'rating')
                    ->orderByDesc('reviews_avg_rating')
                    ->orderByDesc('id');
                break;

            default:
                $query->latest()
                    ->orderByDesc('id');
                break;
        }

        $books = $query
            ->paginate(10)
            ->appends($request->query());
        $genres = Genre::orderBy('name')->get();

        return view('books.index', compact('books', 'genres'));
    }

    public function create(): View
    {
        $genres = Genre::orderBy('name')->get();

        return view('books.create', compact('genres'));
    }

    public function store(StoreBookRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $bookData = collect($validated)
            ->except('genres')
            ->toArray();

        $book = $request->user()
            ->books()
            ->create($bookData);

        $book->genres()->attach($validated['genres']);

        return redirect()
            ->route('books.show', $book)
            ->with('success', '書籍を登録しました。');
    }

    public function show(Book $book): View
    {
        $book->load([
            'reviews.user',
            'reviews.likedByUsers',
            'genres',
        ]);

        return view('books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        $this->authorize('update', $book);

        $genres = Genre::orderBy('name')->get();

        return view('books.edit', compact('book', 'genres'));
    }

    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $this->authorize('update', $book);

        $validated = $request->validated();

        $bookData = collect($validated)
            ->except('genres')
            ->toArray();

        $book->update($bookData);
        $book->genres()->sync($validated['genres']);

        return redirect()
            ->route('books.show', $book)
            ->with('success', '書籍情報を更新しました。');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', '書籍を削除しました。');
    }

    public function searchByIsbn(string $isbn): JsonResponse
    {
        if (! preg_match('/^\d{13}$/', $isbn)) {
            return response()->json([
                'error' => 'ISBNは13桁の数字で入力してください。',
            ], 422);
        }

        $apiKey = config('services.google_books.api_key');
        $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:{$isbn}";

        if ($apiKey) {
            $url .= "&key={$apiKey}";
        }

        try {
            $response = Http::get($url);

            if ($response->status() === 429) {
                return response()->json([
                    'error' => '書籍情報の取得回数が上限に達しました。時間をおいて再度お試しください。',
                ], 429);
            }

            if ($response->failed()) {
                return response()->json([
                    'error' => '書籍情報の取得に失敗しました。時間をおいて再度お試しください。',
                ], 500);
            }

            $data = $response->json();

            if (! isset($data['items'][0])) {
                return response()->json([
                    'error' => '書籍が見つかりませんでした。',
                ], 404);
            }

            $volumeInfo = $data['items'][0]['volumeInfo'];

            return response()->json([
                'title' => $volumeInfo['title'] ?? '',
                'author' => isset($volumeInfo['authors']) ? implode(', ', $volumeInfo['authors']) : '',
                'published_date' => $volumeInfo['publishedDate'] ?? '',
                'description' => $volumeInfo['description'] ?? '',
                'image_url' => $volumeInfo['imageLinks']['thumbnail'] ?? '',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => '書籍情報の取得に失敗しました。時間をおいて再度お試しください。'], 500);
        }
    }
}
