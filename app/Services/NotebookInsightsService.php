<?php

namespace App\Services;

use App\Models\Notebook;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class NotebookInsightsService
{
    public function __construct(protected GeminiService $openAI) {}

    /**
     * Build dashboard and workspace insights for a notebook.
     *
     * @return array<string, mixed>
     */
    public function buildWorkspace(Notebook $notebook): array
    {
        $combinedText = $notebook->sources
            ->pluck('summary')
            ->filter()
            ->implode("\n\n");

        $summary = $notebook->summary
            ?: $this->openAI->summarize($combinedText, 'Focus on decisions, actions, risks, and governance implications.');

        $suggestedQuestions = $this->suggestedQuestions($notebook);
        $smartTags = $this->smartTags($notebook);

        return [
            'summary' => $summary,
            'smart_tags' => $smartTags,
            'suggested_questions' => $suggestedQuestions,
            'title_suggestion' => $this->titleSuggestion($notebook),
        ];
    }

    /**
     * Generate suggested notebook questions.
     *
     * @return array<int, string>
     */
    public function suggestedQuestions(Notebook $notebook): array
    {
        $sourceTypes = $notebook->sources->pluck('type')->unique()->implode(', ');
        $base = [
            'Summarize the main governance issues captured in this notebook.',
            'List action items and accountable offices from these sources.',
            'Draft a policy brief based on the uploaded materials.',
            'What risks, gaps, or unresolved questions appear across the documents?',
        ];

        if (Str::contains($sourceTypes, 'youtube') || Str::contains($sourceTypes, 'audio')) {
            $base[] = 'Turn the meeting materials into concise meeting notes.';
        }

        if ($notebook->sources->count() > 1) {
            $base[] = 'Compare the uploaded sources and identify conflicts or overlaps.';
        }

        return $base;
    }

    /**
     * Generate smart tags from notebook content.
     *
     * @return array<int, string>
     */
    public function smartTags(Notebook $notebook): array
    {
        if (filled($notebook->smart_tags)) {
            return $notebook->smart_tags;
        }

        $terms = collect([
            $notebook->category?->name,
            ...$notebook->sources->pluck('type')->all(),
            ...Str::of($notebook->title)->lower()->explode(' ')->all(),
        ])
            ->filter()
            ->map(fn (string $tag) => Str::headline(trim($tag)))
            ->unique()
            ->take(6)
            ->values()
            ->all();

        return $terms;
    }

    /**
     * Generate a lightweight AI title suggestion.
     */
    public function titleSuggestion(Notebook $notebook): string
    {
        if (filled($notebook->ai_title_suggestion)) {
            return $notebook->ai_title_suggestion;
        }

        $category = $notebook->category?->name ?: 'Governance';

        return "{$category} Knowledge Brief";
    }

    /**
     * Build quick stats for charts.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function activitySeries(Notebook $notebook): Collection
    {
        return $notebook->activityLogs
            ->groupBy(fn ($log) => $log->created_at?->format('M d'))
            ->map(fn (Collection $logs, string $day) => [
                'day' => $day,
                'count' => $logs->count(),
            ])
            ->values();
    }
}
