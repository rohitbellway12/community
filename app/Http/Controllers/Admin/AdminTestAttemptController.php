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

    public function export(Request $request)
    {
        $testId = $request->input('test_id');
        $levelId = $request->input('level_id');
        $result = $request->input('result');
        $submissionType = $request->input('submission_type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $query = TestAttempt::with(['user', 'test', 'test.testLevel'])
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

        $attempts = $query->get();

        $filename = 'test_results_export_' . date('Y_m_d_His') . '.xls';

        $output = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        $output .= '<head>';
        $output .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        $output .= '<style>';
        $output .= 'table { border-collapse: collapse; width: 100%; font-family: Calibri, Arial, sans-serif; font-size: 11pt; }';
        $output .= 'th { background-color: #0b1329; color: #ffffff; font-weight: bold; border: 1px solid #cbd5e1; padding: 8px 12px; text-align: left; }';
        $output .= 'td { border: 1px solid #e2e8f0; padding: 6px 10px; vertical-align: middle; }';
        $output .= '</style>';
        $output .= '</head>';
        $output .= '<body>';
        $output .= '<table>';
        $output .= '<thead><tr>';
        $output .= '<th>S.No.</th>';
        $output .= '<th>Student Name</th>';
        $output .= '<th>Student Email</th>';
        $output .= '<th>Test Title</th>';
        $output .= '<th>Test Level</th>';
        $output .= '<th>Score Obtained</th>';
        $output .= '<th>Total Marks</th>';
        $output .= '<th>Percentage</th>';
        $output .= '<th>Result Slab / Grade</th>';
        $output .= '<th>Result Status</th>';
        $output .= '<th>Submission Type</th>';
        $output .= '<th>Tab Switches</th>';
        $output .= '<th>Submitted At</th>';
        $output .= '</tr></thead>';
        $output .= '<tbody>';

        foreach ($attempts as $attempt) {
            $slab = \App\Models\ResultSlab::getSlabForScore((float)$attempt->score_obtained, $attempt->test_id);
            $slabName = $slab ? $slab->name : 'N/A';

            $output .= '<tr>';
            $output .= '<td>#' . $attempt->id . '</td>';
            $output .= '<td>' . htmlspecialchars($attempt->user?->name ?? 'Unknown', ENT_QUOTES, 'UTF-8') . '</td>';
            $output .= '<td>' . htmlspecialchars($attempt->user?->email ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $output .= '<td>' . htmlspecialchars($attempt->test?->title ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $output .= '<td>' . htmlspecialchars($attempt->test?->testLevel?->name ?? '', ENT_QUOTES, 'UTF-8') . '</td>';
            $output .= '<td>' . $attempt->score_obtained . '</td>';
            $output .= '<td>' . ($attempt->test?->total_marks ?? 0) . '</td>';
            $output .= '<td>' . $attempt->percentage . '%</td>';
            $output .= '<td>' . htmlspecialchars($slabName, ENT_QUOTES, 'UTF-8') . '</td>';
            $output .= '<td>' . strtoupper($attempt->result) . '</td>';
            $output .= '<td>' . ucfirst(str_replace('_', ' ', $attempt->submission_type)) . '</td>';
            $output .= '<td>' . ($attempt->tab_switch_count ?? 0) . '</td>';
            $output .= '<td>' . ($attempt->submitted_at?->format('Y-m-d H:i:s') ?? '') . '</td>';
            $output .= '</tr>';
        }

        $output .= '</tbody></table></body></html>';

        return response($output, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    public function show(TestAttempt $attempt): View
    {
        $attempt->load(['user', 'test', 'test.testLevel', 'answers.question', 'answers.option']);

        return view('admin.test-attempts.show', compact('attempt'));
    }
}
