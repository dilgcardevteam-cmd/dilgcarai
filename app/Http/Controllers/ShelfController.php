<?php

namespace App\Http\Controllers;

use App\Models\Notebook;
use App\Models\Shelf;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ShelfController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $shelf = $request->user()->shelves()->create([
            'name' => $request->name,
            'icon' => $request->icon ?? '📚',
            'color' => $request->color,
        ]);

        return redirect()->back()->with('status', "Shelf {$shelf->name} created!");
    }

    public function update(Request $request, Shelf $shelf)
    {
        $this->authorize('update', $shelf);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_collapsed' => 'boolean',
        ]);

        $shelf->update($request->only('name', 'icon', 'color', 'is_collapsed'));
        
        return redirect()->back()->with('status', "Shelf {$shelf->name} updated!");
    }

    public function destroy(Shelf $shelf)
    {
        $this->authorize('delete', $shelf);
        
        $name = $shelf->name;
        $shelf->delete();
        
        return redirect()->back()->with('status', "Shelf {$name} deleted!");
    }

    public function addNotebook(Request $request, Shelf $shelf)
    {
        $this->authorize('update', $shelf);
        
        $request->validate([
            'notebook_id' => ['required', Rule::exists('notebooks', 'id')],
        ]);
        
        $notebook = Notebook::findOrFail($request->notebook_id);
        
        if ($notebook->owner_id !== auth()->id()) {
            abort(403);
        }
        
        $shelf->notebooks()->syncWithoutDetaching([$notebook->id => ['order' => $shelf->notebooks()->count()]]);
        
        return redirect()->back()->with('status', "Notebook added to shelf!");
    }

    public function removeNotebook(Shelf $shelf, Notebook $notebook)
    {
        $this->authorize('update', $shelf);
        
        $shelf->notebooks()->detach($notebook);
        
        return redirect()->back()->with('status', "Notebook removed from shelf!");
    }
}

