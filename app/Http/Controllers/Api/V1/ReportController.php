<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Report;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    use ApiResponse;

    protected const TYPE_MODEL_MAP = [
        'post'    => Post::class,
        'comment' => Comment::class,
        'user'    => User::class,
    ];

    /**
     * Report an abusive or inappropriate post, comment, or user.
     *
     * POST /api/v1/reports
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type'        => ['required', 'string', 'in:post,comment,user'],
            'id'          => ['required', 'integer'],
            'reason'      => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $modelClass = self::TYPE_MODEL_MAP[$validated['type']];
        $target = $modelClass::find($validated['id']);

        if (!$target) {
            return $this->errorResponse(ucfirst($validated['type']) . ' not found.', 404);
        }

        $user = $request->user();

        // Prevent reporting oneself
        if ($validated['type'] === 'user' && (int) $target->id === (int) $user->id) {
            return $this->errorResponse('You cannot report yourself.', 422);
        }

        // Prevent duplicate pending reports by the same user on the same item
        $alreadyReported = Report::where('reporter_id', $user->id)
            ->where('reportable_type', $modelClass)
            ->where('reportable_id', $target->id)
            ->whereIn('status', [ReportStatus::PENDING->value, ReportStatus::REVIEWING->value])
            ->exists();

        if ($alreadyReported) {
            return $this->successResponse(
                ['reported' => true],
                'You have already reported this content. Our moderation team is reviewing it.',
                200
            );
        }

        $report = Report::create([
            'reporter_id'     => $user->id,
            'reportable_type' => $modelClass,
            'reportable_id'   => $target->id,
            'reason'          => trim($validated['reason']),
            'description'     => isset($validated['description']) ? trim($validated['description']) : null,
            'status'          => ReportStatus::PENDING,
        ]);

        return $this->successResponse([
            'id'         => $report->id,
            'type'       => $validated['type'],
            'target_id'  => $target->id,
            'status'     => $report->status->value,
            'created_at' => $report->created_at?->toIso8601String(),
        ], 'Report submitted successfully. Thank you for keeping our community safe.', 201);
    }
}
