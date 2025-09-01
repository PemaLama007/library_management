<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookIssue;
use App\Models\Student;

/**
 * Simple Book Recommendation Engine
 * Works without complex user roles - based on borrowing patterns
 */
class SimpleBookRecommendation
{
    /**
     * Get book recommendations for a student
     * Based on: categories they've borrowed, popular books, similar students
     */
    public function getRecommendationsForStudent($studentId, $limit = 5)
    {
        $student = Student::find($studentId);
        if (!$student) {
            return [];
        }

        // Get books the student has already borrowed
        $borrowedBookIds = $student->bookIssues()->pluck('book_id')->toArray();

        // Get categories the student has shown interest in
        $preferredCategories = Book::whereIn('id', $borrowedBookIds)
            ->pluck('category_id')
            ->unique()
            ->toArray();

        $recommendations = [];

        // Strategy 1: Books from preferred categories (40% weight)
        $categoryRecommendations = $this->getRecommendationsByCategory($preferredCategories, $borrowedBookIds, 3);
        foreach ($categoryRecommendations as $book) {
            $recommendations[$book->id] = ($recommendations[$book->id] ?? 0) + 40;
        }

        // Strategy 2: Popular books among similar students (35% weight) 
        $popularRecommendations = $this->getPopularBooks($borrowedBookIds, 3);
        foreach ($popularRecommendations as $book) {
            $recommendations[$book->id] = ($recommendations[$book->id] ?? 0) + 35;
        }

        // Strategy 3: Books by same authors (25% weight)
        $authorRecommendations = $this->getRecommendationsByAuthor($borrowedBookIds, 2);
        foreach ($authorRecommendations as $book) {
            $recommendations[$book->id] = ($recommendations[$book->id] ?? 0) + 25;
        }

        // Sort by recommendation score and get top results
        arsort($recommendations);
        $topRecommendationIds = array_slice(array_keys($recommendations), 0, $limit);

        return Book::with(['author', 'category', 'publisher'])
            ->whereIn('id', $topRecommendationIds)
            ->where('status', 'Y') // Only available books
            ->get()
            ->map(function($book) use ($recommendations) {
                $book->recommendation_score = $recommendations[$book->id];
                $book->recommendation_reason = $this->getRecommendationReason($book, $recommendations[$book->id]);
                return $book;
            });
    }

    /**
     * Get recommendations based on preferred categories
     */
    private function getRecommendationsByCategory($categoryIds, $excludeBookIds, $limit)
    {
        if (empty($categoryIds)) {
            return collect([]);
        }

        return Book::whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $excludeBookIds)
            ->where('status', 'Y')
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    /**
     * Get popular books (most borrowed)
     */
    private function getPopularBooks($excludeBookIds, $limit)
    {
        return Book::withCount('bookIssues')
            ->whereNotIn('id', $excludeBookIds)
            ->where('status', 'Y')
            ->orderBy('book_issues_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get recommendations by same authors
     */
    private function getRecommendationsByAuthor($borrowedBookIds, $limit)
    {
        // Get authors of borrowed books
        $authorIds = Book::whereIn('id', $borrowedBookIds)
            ->pluck('auther_id') // Note: using 'auther_id' as per your DB schema
            ->unique()
            ->toArray();

        if (empty($authorIds)) {
            return collect([]);
        }

        return Book::whereIn('auther_id', $authorIds)
            ->whereNotIn('id', $borrowedBookIds)
            ->where('status', 'Y')
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }

    /**
     * Get recommendation reason based on score composition
     */
    private function getRecommendationReason($book, $score)
    {
        if ($score >= 70) {
            return "Perfect match based on your reading preferences";
        }
        if ($score >= 40) {
            return "Popular in your favorite categories";
        }
        if ($score >= 25) {
            return "By authors you've enjoyed before";
        }
        return "Trending in the library";
    }

    /**
     * Get trending books (recently popular)
     */
    public function getTrendingBooks($limit = 10)
    {
        return Book::withCount(['bookIssues' => function($query) {
                $query->where('issue_date', '>=', now()->subDays(30));
            }])
            ->where('status', 'Y')
            ->orderBy('book_issues_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get new arrivals (recently added books)
     */
    public function getNewArrivals($limit = 10)
    {
        return Book::where('status', 'Y')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get books similar to a specific book
     */
    public function getSimilarBooks($bookId, $limit = 5)
    {
        $book = Book::find($bookId);
        if (!$book) {
            return collect([]);
        }

        // Find books with same category or author
        return Book::where(function($query) use ($book) {
                $query->where('category_id', $book->category_id)
                      ->orWhere('auther_id', $book->auther_id);
            })
            ->where('id', '!=', $bookId)
            ->where('status', 'Y')
            ->inRandomOrder()
            ->take($limit)
            ->get();
    }
}
