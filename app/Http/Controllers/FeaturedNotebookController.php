<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeaturedNotebookController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search', '');

        $featuredNotebooks = Notebook::query()
            ->with(['owner', 'category'])
            ->withCount('sources')
            ->where('is_featured', true)
            ->orderBy('display_order')
            ->orderByDesc('featured_at')
            ->get();

        $allNotebooks = Notebook::query()
            ->with(['owner', 'category'])
            ->withCount('sources')
            ->where('is_featured', false)
            ->whereHas('owner', function($q) {
                $q->where('role', 'admin');
            })
            ->when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->get();

        return view('featured-notebooks.index', compact('featuredNotebooks', 'allNotebooks', 'search'));
    }

    public function add(Request $request, Notebook $notebook): RedirectResponse
    {
        $maxOrder = Notebook::query()
            ->where('is_featured', true)
            ->max('display_order');

        $notebook->update([
            'is_featured' => true,
            'featured_at' => now(),
            'display_order' => ($maxOrder ?? 0) + 1,
        ]);

        return redirect()
            ->route('featured-notebooks.index')
            ->with('status', 'Notebook added to featured list.');
    }

    public function remove(Request $request, Notebook $notebook): RedirectResponse
    {
        $notebook->update([
            'is_featured' => false,
            'featured_at' => null,
            'display_order' => null,
        ]);

        return redirect()
            ->route('featured-notebooks.index')
            ->with('status', 'Notebook removed from featured list.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:notebooks,id',
        ]);

        foreach ($request->order as $index => $notebookId) {
            Notebook::where('id', $notebookId)->update([
                'display_order' => $index + 1,
            ]);
        }

        return redirect()
            ->route('featured-notebooks.index')
            ->with('status', 'Featured notebooks reordered.');
    }
}
