<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Services\DynamicFineCalculator;
use App\Services\LibraryFraudDetection;
use App\Services\SimpleBookRecommendation;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Library Management Algorithm APIs
Route::prefix('library')->group(function () {
    
    // Fine Calculation API
    Route::post('/calculate-fine', function (Request $request) {
        $calculator = new DynamicFineCalculator();
        
        $result = $calculator->calculateProgressiveFine(
            $request->issue_date,
            $request->return_date,
            $request->actual_return_date
        );
        
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    });
    
    // Fine Remission API
    Route::post('/calculate-remission/{studentId}', function (Request $request, $studentId) {
        $calculator = new DynamicFineCalculator();
        
        $result = $calculator->calculateFineRemission(
            $studentId,
            $request->current_fine
        );
        
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    });
    
    // Fraud Detection API
    Route::get('/fraud-check/{studentId}', function ($studentId) {
        $fraudDetection = new LibraryFraudDetection();
        
        $result = $fraudDetection->detectStudentAnomalies($studentId);
        
        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    });
    
    // Bulk Fraud Detection API
    Route::get('/bulk-fraud-check', function (Request $request) {
        $fraudDetection = new LibraryFraudDetection();
        
        $limit = $request->get('limit', 50);
        $results = $fraudDetection->bulkFraudDetection($limit);
        
        return response()->json([
            'success' => true,
            'data' => $results,
            'total_flagged' => count($results)
        ]);
    });
    
    // Book Recommendations API
    Route::get('/recommendations/{studentId}', function ($studentId, Request $request) {
        $recommendationEngine = new SimpleBookRecommendation();
        
        $limit = $request->get('limit', 5);
        $recommendations = $recommendationEngine->getRecommendationsForStudent($studentId, $limit);
        
        return response()->json([
            'success' => true,
            'data' => $recommendations
        ]);
    });
    
    // Trending Books API
    Route::get('/trending-books', function (Request $request) {
        $recommendationEngine = new SimpleBookRecommendation();
        
        $limit = $request->get('limit', 10);
        $trending = $recommendationEngine->getTrendingBooks($limit);
        
        return response()->json([
            'success' => true,
            'data' => $trending
        ]);
    });
    
    // Similar Books API
    Route::get('/similar-books/{bookId}', function ($bookId, Request $request) {
        $recommendationEngine = new SimpleBookRecommendation();
        
        $limit = $request->get('limit', 5);
        $similar = $recommendationEngine->getSimilarBooks($bookId, $limit);
        
        return response()->json([
            'success' => true,
            'data' => $similar
        ]);
    });
    
});
