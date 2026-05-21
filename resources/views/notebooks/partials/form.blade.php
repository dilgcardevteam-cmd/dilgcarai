@php
    $editing = isset($notebook);
@endphp

<div class="grid gap-6 xl:grid-cols-[1.1fr_.9fr]">
    <div class="panel p-6">
        <div class="space-y-6">
            <div>
                <x-input-label for="title" value="Notebook title" />
                <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $notebook->title ?? '')" required autofocus />
                <x-input-error :messages="$errors->get('title')" />
            </div>

            <div>
                <x-input-label for="summary" value="Executive summary" />
                <textarea id="summary" name="summary" rows="4" class="input-shell mt-1">{{ old('summary', $notebook->summary ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('summary')" />
            </div>

            <div>
                <x-input-label for="description" value="Description and scope" />
                <textarea id="description" name="description" rows="8" class="input-shell mt-1">{{ old('description', $notebook->description ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('description')" />
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="panel p-6">
            <h2 class="text-xl font-bold text-white">Notebook settings</h2>
            <div class="mt-5 grid gap-5">
                <div>
                    <x-input-label for="category_id" value="Category" />
                    <select id="category_id" name="category_id" class="input-shell mt-1">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id', $notebook->category_id ?? '') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" />
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="input-shell mt-1">
                            <option value="draft" @selected(old('status', $notebook->status ?? 'active') === 'draft')>Draft</option>
                            <option value="active" @selected(old('status', $notebook->status ?? 'active') === 'active')>Active</option>
                            <option value="archived" @selected(old('status', $notebook->status ?? 'active') === 'archived')>Archived</option>
                        </select>
                        <x-input-error :messages="$errors->get('status')" />
                    </div>
                    <div class="panel-muted px-4 py-4">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Access</p>
                        <p class="mt-2 text-sm text-slate-200">Every notebook now opens directly in the shared workspace without account sign-in.</p>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <x-input-label for="icon" value="Notebook icon" />
                        <x-text-input id="icon" name="icon" type="text" class="mt-1 block w-full" :value="old('icon', $notebook->icon ?? 'sparkles')" />
                        <x-input-error :messages="$errors->get('icon')" />
                    </div>
                    <div>
                        <x-input-label for="cover_color" value="Cover color" />
                        <x-text-input id="cover_color" name="cover_color" type="text" class="mt-1 block w-full" :value="old('cover_color', $notebook->cover_color ?? '#1f6feb')" />
                        <x-input-error :messages="$errors->get('cover_color')" />
                    </div>
                </div>
            </div>
        </div>

        <div class="panel p-6">
            <p class="text-xs uppercase tracking-[0.24em] text-slate-400">AI Workspace Tip</p>
            <p class="mt-3 text-sm leading-7 text-slate-300">After creating the notebook, upload source files and use the AI workspace to summarize, compare, and extract action items from your governance materials.</p>
        </div>

        <button type="submit" class="btn-primary w-full">{{ $editing ? 'Save Notebook Changes' : 'Create Notebook Workspace' }}</button>
    </div>
</div>
