<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    use ApiResponse;

    /**
     * Get list of active countries.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $countries = Country::query()
            ->orderBy('name')
            ->get();

        return $this->successResponse(
            CountryResource::collection($countries),
            'Countries fetched successfully.'
        );
    }
}
