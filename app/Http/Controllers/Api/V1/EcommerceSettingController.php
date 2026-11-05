<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EcommerceSettingController extends Controller
{
    use ApiResponse;

    /**
     * Get E-commerce button configuration for mobile app.
     * Tells the app whether to display the ecommerce button, its title, and destination url.
     */
    public function show(Request $request): JsonResponse
    {
        $setting = AnalyticsSetting::config();

        $data = [
            'is_enabled'   => (bool) ($setting->ecommerce_enabled && !empty($setting->ecommerce_url)),
            'button_label' => (string) ($setting->ecommerce_button_label ?: 'Shop'),
            'store_url'    => $setting->ecommerce_url ? (string) $setting->ecommerce_url : null,
        ];

        return $this->successResponse(
            $data,
            'E-commerce settings retrieved successfully.'
        );
    }
}

