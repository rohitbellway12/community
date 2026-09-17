<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTestAttemptController extends Controller
{
    public function index(Request $request): View
    {
        $testId = $request->input('test_id');
        $levelId = $request->input('level_id');
        $result = $request->input('result');
        $submissionType = $request->input('submission_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = TestAttempt::with(['user', 'test', 'test.testLevel', 'answers'])
            ->orderByDesc('created_at');

        if ($testId) {
            $query->where('test_id', $testId);
        }

        if ($levelId) {
            $query->whereHas('test', function ($q) use ($levelId) {
                $q->where('test_level_id', $levelId);
            });
        }

        if ($result) {
            $query->where('result', $result);
        }

        if ($submissionType) {
            $query->where('submission_type', $submissionType);
        }

        if ($dateFrom) {
            $query->whereDate('submitted_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('submitted_at', '<=', $dateTo);
        }

        $attempts = $query->paginate(20)->withQueryString();
        $tests = Test::where('status', 'published')->orderBy('title')->get();
        $levels = \App\Models\TestLevel::where('status', 'active')->orderBy('name')->get();

        return view('admin.test-attempts.index', compact(
            'attempts', 'tests', 'levels',
            'testId', 'levelId', 'result', 'submissionType', 'dateFrom', 'dateTo'
        ));
    }

    public function show(TestAttempt $attempt): View
    {
        $attempt->load(['user', 'test', 'test.testLevel', 'answers.question', 'answers.option']);

        return view('admin.test-attempts.show', compact('attempt'));
    }
}
