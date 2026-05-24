<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WelcomeController extends Controller
{
    public function index()
    {
        $products   = Product::with('category')->orderBy('category_id')->get();
        $categories = Category::orderBy('id')->get();
        $storeInfo  = $this->calcStoreStatus();

        $googleReviews = Cache::remember('google_reviews_corndogku', 86400, function () {
            $apiKey  = env('GOOGLE_PLACES_API_KEY');
            $placeId = env('GOOGLE_PLACE_ID');

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

                return $response->successful()
                    ? ($response->json('result.reviews') ?? [])
                    : [];
            } catch (\Exception $e) {
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
