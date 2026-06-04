<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WelcomeController extends Controller
{
    public function index()
    {
        $products   = Product::with('category')
            ->where('is_custom', false)
            ->whereHas('category', fn($q) => $q->whereRaw('LOWER(name) != ?', ['custom']))
            ->orderBy('category_id')
            ->get();
        $categories = Category::whereRaw('LOWER(name) != ?', ['custom'])->orderBy('id')->get();
        $storeInfo  = $this->calcStoreStatus();

        $googleReviews = Cache::remember('google_reviews_corndogku', 86400, function () {
            $apiKey  = config('services.google_places.key');
            $placeId = config('services.google_places.place_id');

            if (!$apiKey || !$placeId) {
                return [];
            }

            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                    'place_id' => $placeId,
                    'fields'   => 'reviews',
                    'key'      => $apiKey,
                    'language' => 'id',
                ]);

                Log::info('Google Places API called', [
                    'status'  => $response->status(),
                    'success' => $response->successful(),
                    'body'    => $response->json(),
                ]);

                $reviews = $response->successful()
                    ? ($response->json('result.reviews') ?? [])
                    : [];

                Log::info('Google reviews fetched', ['count' => count($reviews)]);

                return $reviews;
            } catch (\Exception $e) {
                Log::error('Google Places API error', ['error' => $e->getMessage()]);
                return [];
            }
        });

        foreach ($googleReviews as &$review) {
            $review['formatted_time'] = isset($review['time'])
                ? Carbon::createFromTimestamp($review['time'])->translatedFormat('d F Y')
                : '';
        }
        unset($review);

        return view('customer.home', compact('products', 'categories', 'storeInfo', 'googleReviews'));
    }
}
