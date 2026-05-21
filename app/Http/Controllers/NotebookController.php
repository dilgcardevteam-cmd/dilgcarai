<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotebookRequest;
use App\Http\Requests\UpdateNotebookRequest;
use App\Models\Category;
use App\Models\Chat;
use App\Models\Notebook;
use App\Services\ActivityLogger;
use App\Services\NotebookInsightsService;
use App\Support\WorkspaceUserResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotebookController extends Controller
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected NotebookInsightsService $insights,
        protected WorkspaceUserResolver $workspaceUserResolver,
    ) {}

    /**
     * Display a listing of the notebooks.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $featuredNotebooks = Notebook::query()
            ->with(['owner', 'category'])
            ->withCount(['sources', 'chats', 'members'])
            ->where('is_featured', true)
            ->orderBy('display_order')
            ->orderByDesc('featured_at')
            ->get();

        $pinnedNotebooks = Notebook::query()
            ->with(['owner', 'category'])
            ->withCount(['sources', 'chats', 'members'])
            ->where('is_pinned', true)
            ->where('owner_id', $user->id)
            ->orderBy('display_order')
            ->orderByDesc('last_activity_at')
            ->get();

        $userNotebooks = Notebook::query()
            ->with(['owner', 'category'])
            ->withCount(['sources', 'chats', 'members'])
            ->where('owner_id', $user->id)
            ->where('is_pinned', false)
            ->orderByDesc('last_activity_at')
            ->get();

        $shelves = $user->shelves()->with('notebooks')->get();

        $sharedNotebooks = $user->sharedNotebooks()->with(['owner', 'category'])->withCount(['sources', 'chats', 'members'])->get();

        return view('notebooks.index', compact('featuredNotebooks', 'pinnedNotebooks', 'userNotebooks', 'shelves', 'sharedNotebooks'));
    }

    /**
     * Create a quick notebook.
     */
    public function create(Request $request): RedirectResponse
    {
        return $this->createQuick($request);
    }

    /**
     * Store a newly created notebook.
     */
    public function store(StoreNotebookRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $notebook = Notebook::create([
            ...$data,
            'owner_id' => $user->id,
            'slug' => $this->uniqueSlug($data['title']),
            'shared_token' => ($data['visibility'] ?? 'private') === 'shared' ? Str::random(40) : null,
            'smart_tags' => $data['smart_tags'] ?? null,
            'last_activity_at' => now(),
        ]);

        $chat = $notebook->chats()->create([
            'user_id' => null,
            'title' => 'Primary workspace',
            'mode' => 'qa',
            'context_summary' => $notebook->summary,
            'last_message_at' => now(),
        ]);

        $this->activityLogger->log(
            null,
            'notebook.created',
            "Created notebook {$notebook->title}.",
            $notebook,
            null,
            ['chat_id' => $chat->id],
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('notebooks.show', $notebook)
            ->with('status', 'Notebook created successfully.');
    }

    /**
     * Create a quick notebook.
     */
    public function createQuick(Request $request): RedirectResponse
    {
        $user = $request->user();

        $notebook = Notebook::create([
            'owner_id' => $user->id,
            'title' => 'Untitled notebook',
            'summary' => 'Quick notebook workspace',
            'description' => '',
            'category_id' => null,
            'status' => 'active',
            'visibility' => 'private',
            'icon' => 'sparkles',
            'cover_color' => '#8b5cf6',
            'slug' => $this->uniqueSlug('Untitled notebook'),
            'last_activity_at' => now(),
        ]);

        $chat = $notebook->chats()->create([
            'user_id' => null,
            'title' => 'Primary workspace',
            'mode' => 'qa',
            'context_summary' => $notebook->summary,
            'last_message_at' => now(),
        ]);

        $this->activityLogger->log(
            null,
            'notebook.created',
            "Created notebook {$notebook->title}.",
            $notebook,
            null,
            ['chat_id' => $chat->id],
            $request->ip(),
            $request->userAgent()
        );

        return redirect()->route('notebooks.show', $notebook);
    }

    /**
     * Display the specified notebook.
     */
    public function show(Request $request, Notebook $notebook): View
    {
        $notebook->load([
            'category',
            'activityLogs.user',
            'chats.messages.user',
            'memberships.user',
        ]);

        $sources = $notebook->sources()
            ->latest()
            ->paginate(3, ['*'], 'sources_page')
            ->withQueryString();

        $sourcesTotal = $notebook->sources()->count();

        /** @var Chat $activeChat */
        $activeChat = $notebook->chats()
            ->with(['messages.user'])
            ->find($request->integer('chat'))
            ?? $notebook->chats()->with(['messages.user'])->latest('updated_at')->first()
            ?? $notebook->chats()->create([
                'user_id' => null,
                'title' => 'Primary workspace',
                'mode' => 'qa',
                'last_message_at' => now(),
            ]);

        $workspace = $this->insights->buildWorkspace($notebook);

        return view('notebooks.show', [
            'notebook' => $notebook,
            'sources' => $sources,
            'sourcesTotal' => $sourcesTotal,
            'activeChat' => $activeChat,
            'workspace' => $workspace,
            'activitySeries' => $this->insights->activitySeries($notebook),
        ]);
    }

    /**
     * Show the form for editing the notebook.
     */
    public function edit(Notebook $notebook): View
    {
        return view('notebooks.edit', [
            'notebook' => $notebook,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified notebook.
     */
    public function update(Request $request, Notebook $notebook): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $data = [
            'title' => $request->title,
        ];

        if ($data['title'] !== $notebook->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $notebook->id);
        }

        $notebook->update($data);

        $this->activityLogger->log(
            null,
            'notebook.updated',
            "Renamed notebook to {$notebook->title}.",
            $notebook,
            null,
            [],
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('notebooks.index')
            ->with('status', 'Notebook renamed successfully.');
    }

    /**
     * Remove the specified notebook.
     */
    public function destroy(Request $request, Notebook $notebook): RedirectResponse
    {
        $title = $notebook->title;

        $this->activityLogger->log(
            null,
            'notebook.deleted',
            "Deleted notebook {$title}.",
            $notebook,
            null,
            [],
            $request->ip(),
            $request->userAgent()
        );

        $notebook->delete();

        return redirect()
            ->route('notebooks.index')
            ->with('status', "Notebook {$title} deleted.");
    }

    /**
     * Duplicate the specified notebook.
     */
    public function duplicate(Request $request, Notebook $notebook): RedirectResponse
    {
        $user = $request->user();

        $newNotebook = $notebook->replicate([
            'id', 'slug', 'shared_token', 'created_at', 'updated_at', 'last_activity_at',
        ]);

        $newNotebook->title = $notebook->title . ' (Copy)';
        $newNotebook->owner_id = $user->id;
        $newNotebook->slug = $this->uniqueSlug($newNotebook->title);
        $newNotebook->is_featured = false;
        $newNotebook->is_pinned = false;
        $newNotebook->last_activity_at = now();
        $newNotebook->save();

        foreach ($notebook->sources as $source) {
            $newSource = $source->replicate([
                'id', 'created_at', 'updated_at',
            ]);
            $newSource->notebook_id = $newNotebook->id;
            $newSource->save();
        }

        foreach ($notebook->chats as $chat) {
            $newChat = $chat->replicate([
                'id', 'created_at', 'updated_at', 'last_message_at',
            ]);
            $newChat->notebook_id = $newNotebook->id;
            $newChat->last_message_at = now();
            $newChat->save();

            foreach ($chat->messages as $message) {
                $newMessage = $message->replicate([
                    'id', 'created_at', 'updated_at',
                ]);
                $newMessage->chat_id = $newChat->id;
                $newMessage->save();
            }
        }

        $this->activityLogger->log(
            null,
            'notebook.duplicated',
            "Duplicated notebook {$notebook->title}.",
            $newNotebook,
            null,
            ['original_notebook_id' => $notebook->id],
            $request->ip(),
            $request->userAgent()
        );

        return redirect()
            ->route('notebooks.index')
            ->with('status', "Notebook duplicated successfully.");
    }

    /**
     * Bulk actions for notebooks.
     */
    public function bulkActions(Request $request): RedirectResponse
    {
        $request->validate([
            'action' => 'required|in:delete,pin,unpin,share',
            'notebook_ids' => 'required|array|min:1',
            'notebook_ids.*' => 'integer|exists:notebooks,id',
        ]);

        $user = $request->user();
        $notebooks = Notebook::whereIn('id', $request->notebook_ids)
            ->where('owner_id', $user->id)
            ->get();

        switch ($request->action) {
            case 'delete':
                foreach ($notebooks as $notebook) {
                    $notebook->delete();
                }
                $message = 'Selected notebooks deleted.';
                break;

            case 'pin':
                foreach ($notebooks as $notebook) {
                    $notebook->update(['is_pinned' => true]);
                }
                $message = 'Selected notebooks pinned.';
                break;

            case 'unpin':
                foreach ($notebooks as $notebook) {
                    $notebook->update(['is_pinned' => false]);
                }
                $message = 'Selected notebooks unpinned.';
                break;

            case 'share':
                foreach ($notebooks as $notebook) {
                    $notebook->update([
                        'visibility' => 'shared',
                        'shared_token' => Str::random(40),
                    ]);
                }
                $message = 'Selected notebooks shared.';
                break;
        }

        return redirect()
            ->route('notebooks.index')
            ->with('status', $message);
    }

    public function pin(Request $request, Notebook $notebook): RedirectResponse
    {
        if ($notebook->owner_id !== $request->user()->id) {
            abort(403);
        }
        
        $notebook->update(['is_pinned' => true]);
        
        $this->activityLogger->log(
            null,
            'notebook.pinned',
            "Pinned notebook {$notebook->title}.",
            $notebook,
            null,
            [],
            $request->ip(),
            $request->userAgent()
        );
        
        return redirect()->back()->with('status', "Notebook {$notebook->title} pinned!");
    }

    public function unpin(Request $request, Notebook $notebook): RedirectResponse
    {
        if ($notebook->owner_id !== $request->user()->id) {
            abort(403);
        }
        
        $notebook->update(['is_pinned' => false]);
        
        $this->activityLogger->log(
            null,
            'notebook.unpinned',
            "Unpinned notebook {$notebook->title}.",
            $notebook,
            null,
            [],
            $request->ip(),
            $request->userAgent()
        );
        
        return redirect()->back()->with('status', "Notebook {$notebook->title} unpinned!");
    }

    public function updateVisibility(Request $request, Notebook $notebook): RedirectResponse
    {
        if ($notebook->owner_id !== $request->user()->id) {
            abort(403);
        }

        $request->validate([
            'visibility' => 'required|in:restricted,link,public',
        ]);

        $visibility = $request->visibility;
        $sharedToken = $visibility === 'restricted' 
            ? null 
            : ($notebook->shared_token ?? Str::random(40));

        $notebook->update([
            'visibility' => $visibility,
            'shared_token' => $sharedToken,
        ]);

        return redirect()->back()->with('status', 'Notebook visibility updated!');
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (
            Notebook::query()
                ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
