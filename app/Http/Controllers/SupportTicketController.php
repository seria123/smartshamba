<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Crop;
use App\Models\Farm;
use App\Models\Livestock;
use App\Models\SupportKnowledgeArticle;
use App\Models\SupportTicket;
use App\Models\WeatherData;
use App\Services\OpenAIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = SupportTicket::with(['user', 'assignee'])->where('user_id', auth()->id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(15);
        $allTickets = SupportTicket::where('user_id', auth()->id())->get();
        $avgFirstResponse = $allTickets->whereNotNull('first_response_at')->avg('first_response_minutes');
        $avgResolution = $allTickets->whereNotNull('resolved_at')->avg('resolution_minutes');
        $knowledgeArticles = SupportKnowledgeArticle::search();

        return view('support-tickets.index', compact('tickets', 'allTickets', 'avgFirstResponse', 'avgResolution', 'knowledgeArticles'));
    }

    public function create(): View
    {
        $categories = [
            'crop' => 'Crops',
            'livestock' => 'Livestock',
            'finance' => 'Finance',
            'system_bug' => 'System Bugs',
            'farm_management' => 'Farm Management',
            'other' => 'Other',
        ];

        $priorities = [
            'low' => 'Low',
            'medium' => 'Medium',
            'urgent' => 'Urgent',
        ];

        $channels = [
            'in_app' => 'In-app chat',
            'email' => 'Email',
            'sms' => 'SMS',
            'whatsapp' => 'WhatsApp',
        ];

        $agentRoles = [
            'agronomist' => 'Agronomist',
            'vet' => 'Vet',
            'technical_support' => 'Technical Support',
        ];

        return view('support-tickets.create', compact('categories', 'priorities', 'channels', 'agentRoles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'priority' => 'required|in:low,medium,urgent',
            'category' => 'required|in:crop,livestock,finance,system_bug,farm_management,other',
            'support_channel' => 'required|in:in_app,email,sms,whatsapp',
            'assigned_role' => 'nullable|in:agronomist,vet,technical_support',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,mp4,mov,pdf|max:10240',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'open';
        $validated['auto_tags'] = $this->autoTags($validated['subject'].' '.$validated['message']);
        $validated['suggested_solutions'] = $this->suggestSolutions($validated['subject'].' '.$validated['message'], $validated['category']);
        $validated['context_snapshot'] = $this->contextSnapshot();
        $validated['assigned_role'] = $validated['assigned_role'] ?: $this->routeRole($validated['category'], $validated['auto_tags']);
        $validated['sla_due_at'] = now()->addHours($validated['priority'] === 'urgent' ? 4 : ($validated['priority'] === 'medium' ? 24 : 72));

        if ($request->hasFile('media')) {
            $validated['media_paths'] = collect($request->file('media'))
                ->map(fn ($file) => $file->store('support-tickets', 'public'))
                ->values()
                ->all();
        }

        SupportTicket::create($validated);

        return redirect()->route('support-tickets.index')
            ->with('success', 'Support ticket created successfully.');
    }

    public function show(SupportTicket $supportTicket): View
    {
        if ($supportTicket->user_id !== auth()->id()) {
            abort(403, 'You do not have access to this ticket.');
        }

        $supportTicket->load(['user', 'assignee']);

        return view('support-tickets.show', compact('supportTicket'));
    }

    public function knowledgeBase(Request $request): View
    {
        $articles = SupportKnowledgeArticle::search($request->input('q'), $request->input('category'));

        return view('support-tickets.knowledge-base', compact('articles'));
    }

    public function chat(Request $request, OpenAIService $openAIService): View
    {
        $question = $request->input('question');
        $answerChannel = $request->input('answer_channel', 'smart_ai');
        $articles = $question ? SupportKnowledgeArticle::search($question) : SupportKnowledgeArticle::search();
        $context = $this->contextSnapshot();
        $answer = null;
        $answerSource = null;

        if ($question && $answerChannel === 'smart_ai') {
            $answer = $openAIService->answerSupportQuestion($question, $context, $articles);
            $answerSource = $answer ? 'SmartShamba AI' : null;
        }

        if ($question && ! $answer) {
            $answer = $this->chatAnswer($question, $articles, $context);
            $answerSource = 'Local help guide';
        }

        return view('support-tickets.chat', compact('question', 'answer', 'answerChannel', 'answerSource', 'articles', 'context'));
    }

    public function rate(Request $request, SupportTicket $supportTicket): RedirectResponse
    {
        if ($supportTicket->user_id !== auth()->id()) {
            abort(403, 'You do not have access to this ticket.');
        }

        $validated = $request->validate([
            'satisfaction_rating' => 'required|integer|min:1|max:5',
            'satisfaction_comment' => 'nullable|string|max:1000',
        ]);

        $supportTicket->update($validated);

        return redirect()->route('support-tickets.show', $supportTicket)->with('success', 'Thanks for rating this support experience.');
    }

    private function autoTags(string $text): array
    {
        $keywords = [
            'crop' => ['crop', 'maize', 'beans', 'leaf', 'yellow', 'irrigation', 'fertilizer'],
            'livestock' => ['cow', 'goat', 'chicken', 'milk', 'egg', 'vaccine', 'wound', 'sick'],
            'finance' => ['payment', 'invoice', 'mpesa', 'm-pesa', 'expense', 'budget', 'loan'],
            'system_bug' => ['error', 'login', 'bug', 'page', 'not working', 'crash'],
            'urgent' => ['urgent', 'emergency', 'dying', 'outbreak', 'lost money'],
        ];

        $lower = strtolower($text);

        return collect($keywords)
            ->filter(fn ($words) => collect($words)->contains(fn ($word) => str_contains($lower, $word)))
            ->keys()
            ->values()
            ->all();
    }

    private function suggestSolutions(string $text, string $category): array
    {
        $articles = SupportKnowledgeArticle::search($text, $category);

        return collect($articles ?: SupportKnowledgeArticle::search(null, $category))
            ->take(3)
            ->map(fn ($article) => $article['title'].': '.$article['summary'])
            ->values()
            ->all();
    }

    private function routeRole(string $category, array $tags): string
    {
        if ($category === 'crop' || in_array('crop', $tags, true)) {
            return 'agronomist';
        }

        if ($category === 'livestock' || in_array('livestock', $tags, true)) {
            return 'vet';
        }

        return 'technical_support';
    }

    private function contextSnapshot(): array
    {
        $farm = Farm::where('user_id', auth()->id())->latest()->first();

        return [
            'farm' => $farm ? ['name' => $farm->name, 'location' => $farm->location, 'size_hectares' => $farm->size_hectares] : null,
            'crops' => Crop::where('user_id', auth()->id())->latest()->take(5)->pluck('name')->all(),
            'livestock_count' => Livestock::where('user_id', auth()->id())->count(),
            'recent_activities' => Activity::latest()->take(5)->pluck('activity_name')->filter()->values()->all(),
            'latest_weather' => WeatherData::latest()->first()?->only(['temperature', 'humidity', 'precipitation', 'weather_condition', 'recorded_at']),
        ];
    }

    private function chatAnswer(string $question, array $articles, array $context): string
    {
        $best = $articles[0] ?? null;
        $farmName = $context['farm']['name'] ?? 'your farm';

        if (! $best) {
            return "I could not find a close guide yet. Create a ticket and SmartShamba will attach context from {$farmName} automatically.";
        }

        return "Based on {$farmName}, start with: {$best['summary']} Next steps: ".implode(' ', $best['steps']);
    }
}
