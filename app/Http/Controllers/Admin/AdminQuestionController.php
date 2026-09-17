<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\TestLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminQuestionController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $levelId = $request->input('level_id');

        $query = Question::with(['testLevel', 'options'])
            ->orderByDesc('created_at');

        if ($levelId) {
            $query->where('test_level_id', $levelId);
        }

        if ($search) {
            $query->where('question_text', 'like', "%{$search}%");
        }

        $questions = $query->paginate(15)->withQueryString();
        $levels = TestLevel::where('status', 'active')->orderBy('name')->get();

        return view('admin.questions.index', compact('questions', 'levels', 'levelId', 'search'));
    }

    public function create(): View
    {
        $levels = TestLevel::where('status', 'active')->orderBy('name')->get();

        return view('admin.questions.create', compact('levels'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'test_level_id' => [
                'required',
                Rule::exists('test_levels', 'id')->where('status', 'active'),
            ],
            'question_text' => 'required|string|max:2000',
            'question_type' => 'required|in:mcq,true_false,image_based,audio_based',
            'image_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'marks' => 'required|integer|min:1',
            'explanation' => 'nullable|string|max:2000',
            'status' => 'required|in:active,inactive',
            'options' => 'required|array|min:2|max:4',
            'options.*.label' => 'required|in:A,B,C,D',
            'options.*.text' => 'nullable|string|max:500',
            'correct_option' => 'required|in:A,B,C,D',
        ]);

        $question = Question::create([
            'test_level_id' => $validated['test_level_id'],
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'image_url' => $validated['image_url'] ?? null,
            'audio_url' => $validated['audio_url'] ?? null,
            'marks' => $validated['marks'],
            'explanation' => $validated['explanation'] ?? null,
            'status' => $validated['status'],
        ]);

        foreach ($validated['options'] as $opt) {
            if (empty(trim($opt['text'] ?? ''))) {
                continue;
            }
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $opt['text'],
                'option_label' => $opt['label'],
                'is_correct' => ($opt['label'] === $validated['correct_option']),
            ]);
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully.');
    }

    public function edit(Question $question): View
    {
        $levels = TestLevel::where('status', 'active')
            ->orWhere('id', $question->test_level_id)
            ->orderBy('name')
            ->get();
        $options = $question->options->sortBy('option_label');
        $correctOption = $options->firstWhere('is_correct', true)?->option_label ?? 'A';

        return view('admin.questions.edit', compact('question', 'levels', 'options', 'correctOption'));
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'test_level_id' => 'required|exists:test_levels,id',
            'question_text' => 'required|string|max:2000',
            'question_type' => 'required|in:mcq,true_false,image_based,audio_based',
            'image_url' => 'nullable|url',
            'audio_url' => 'nullable|url',
            'marks' => 'required|integer|min:1',
            'explanation' => 'nullable|string|max:2000',
            'status' => 'required|in:active,inactive',
            'options' => 'required|array|min:2|max:4',
            'options.*.label' => 'required|in:A,B,C,D',
            'options.*.text' => 'nullable|string|max:500',
            'correct_option' => 'required|in:A,B,C,D',
        ]);

        $question->update([
            'test_level_id' => $validated['test_level_id'],
            'question_text' => $validated['question_text'],
            'question_type' => $validated['question_type'],
            'image_url' => $validated['image_url'] ?? null,
            'audio_url' => $validated['audio_url'] ?? null,
            'marks' => $validated['marks'],
            'explanation' => $validated['explanation'] ?? null,
            'status' => $validated['status'],
        ]);

        QuestionOption::where('question_id', $question->id)->delete();

        foreach ($validated['options'] as $opt) {
            if (empty(trim($opt['text'] ?? ''))) {
                continue;
            }
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $opt['text'],
                'option_label' => $opt['label'],
                'is_correct' => ($opt['label'] === $validated['correct_option']),
            ]);
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    public function updateStatus(Request $request, Question $question)
    {
        $newStatus = $question->status === 'active' ? 'inactive' : 'active';
        $question->update(['status' => $newStatus]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => 'Question status updated to ' . ucfirst($newStatus),
            ]);
        }

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question status updated to ' . ucfirst($newStatus));
    }

    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully.');
    }
}
