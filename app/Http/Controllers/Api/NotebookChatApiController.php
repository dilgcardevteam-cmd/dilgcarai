<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChatMessageRequest;
use App\Models\Chat;
use App\Models\Notebook;
use App\Services\NotebookRagService;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;

class NotebookChatApiController extends Controller
{
    /**
     * Store a new AI exchange for the notebook chat.
     */
    public function store(
        StoreChatMessageRequest $request,
        Notebook $notebook,
        Chat $chat,
        NotebookRagService $rag,
        GeminiService $gemini,
    ): Response|JsonResponse|StreamedResponse {
        Log::info('NotebookChatApiController: Starting answer pipeline', [
            'notebook_id' => $notebook->id,
            'chat_id' => $chat->id,
        ]);

        abort_unless($chat->notebook_id === $notebook->id, 404);

        $prompt = $request->string('prompt')->trim()->toString();
        $mode = $request->string('mode')->toString() ?: ($chat->mode ?: 'qa');
        $selectedSourceIds = $request->input('selected_source_ids');
        $selectedSourceIds = is_array($selectedSourceIds)
            ? array_values(array_map('intval', array_filter($selectedSourceIds, fn ($id) => is_int($id) || ctype_digit((string) $id))))
            : null;

        Log::info('NotebookChatApiController: Received prompt', [
            'prompt' => $prompt,
            'mode' => $mode,
            'selected_source_ids' => $selectedSourceIds,
        ]);

        $contextPayload = $rag->buildContext($notebook, $prompt, 4, $selectedSourceIds ?: null);

        Log::info('NotebookChatApiController: Context built', [
            'context_length' => strlen($contextPayload['context']),
            'citations_count' => count($contextPayload['citations']),
        ]);

        $userMessage = $chat->messages()->create([
            'user_id' => null,
            'role' => 'user',
            'content' => $prompt,
            'metadata' => ['mode' => $mode, 'selected_source_ids' => $selectedSourceIds ?: null],
        ]);

        Log::info('NotebookChatApiController: Calling GeminiService for answer');

        $answer = $gemini->answer($prompt, $contextPayload['context'], $contextPayload['citations'], $mode);

        Log::info('NotebookChatApiController: Received final answer from AI', [
            'answer_text' => $answer['text'],
            'answer_provider' => $answer['provider'],
        ]);

        $answerCitations = collect($answer['citations'] ?? []);
        $uploadedSources = $answerCitations->filter(fn ($c) => !in_array($c['type'] ?? '', ['web', 'url', 'youtube']))->values();
        $webSources = $answerCitations->filter(fn ($c) => in_array($c['type'] ?? '', ['web', 'url', 'youtube']))->values();
        $webSourceUrls = $webSources->pluck('source_url')->filter()->values()->all();
        $uploadedSourceNames = $uploadedSources->pluck('source_name')->values()->all();
        $uploadedSourceLinks = $uploadedSources
            ->map(function ($source) use ($notebook) {
                $sourceId = $source['source_id'] ?? null;

                return [
                    'name' => $source['source_name'] ?? 'Uploaded source',
                    'url' => $sourceId
                        ? URL::route('notebooks.sources.show', [$notebook, $sourceId])
                        : ($source['source_url'] ?? null),
                ];
            })
            ->filter(fn ($source) => filled($source['url']))
            ->values()
            ->all();
        
        $assistantMessage = $chat->messages()->create([
            'role' => 'assistant',
            'content' => $answer['text'],
            'citations' => $answer['citations'],
            'metadata' => [
                'provider' => $answer['provider'], 
                'mode' => $mode,
                'used_sources' => $answer['used_sources'] ?? false,
                'uploaded_sources_count' => $uploadedSources->count(),
                'uploaded_source_names' => $uploadedSourceNames,
                'uploaded_source_links' => $uploadedSourceLinks,
                'web_sources_count' => $webSources->count(),
                'web_source_urls' => $webSourceUrls,
            ],
        ]);

        $chat->update([
            'mode' => $mode,
            'last_message_at' => now(),
            'title' => $chat->title === 'Primary workspace' ? str($prompt)->limit(48)->toString() : $chat->title,
        ]);

        if ($request->boolean('stream')) {
            Log::info('NotebookChatApiController: Streaming response to frontend');

            return response()->stream(function () use ($assistantMessage): void {
                foreach (str_split($assistantMessage->content, 140) as $chunk) {
                    echo 'data: '.json_encode(['chunk' => $chunk])."\n\n";
                    @ob_flush();
                    flush();
                    usleep(20000);
                }

                echo 'data: '.json_encode([
                    'done' => true,
                    'message' => [
                        'id' => $assistantMessage->id,
                        'content' => $assistantMessage->content,
                        'citations' => $assistantMessage->citations,
                        'metadata' => $assistantMessage->metadata,
                    ],
                ])."\n\n";
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
            ]);
        }

        Log::info('NotebookChatApiController: Returning final response to frontend');

        return response()->json([
            'message' => 'AI response generated successfully.',
            'user_message' => $userMessage,
            'assistant_message' => $assistantMessage,
            'context' => $contextPayload,
        ]);
    }
}
