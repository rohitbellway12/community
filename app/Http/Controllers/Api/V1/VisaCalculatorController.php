<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\VisaCalculatorService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisaCalculatorController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected VisaCalculatorService $calculatorService
    ) {}

    /**
     * Get all F-2-7 visa criteria, options, and point values.
     */
    public function options(): JsonResponse
    {
        $options = $this->calculatorService->getOptions();

        return $this->successResponse(
            $options,
            'Visa calculator options retrieved successfully.'
        );
    }

    /**
     * Calculate F-2-7 visa points based on submitted criteria.
     */
    public function calculate(Request $request): JsonResponse
    {
        $result = $this->calculatorService->calculate($request->all());

        return $this->successResponse(
            $result,
            'Visa points calculated successfully.'
        );
    }
}
