<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNotebookMemberRequest;
use App\Models\Notebook;
use App\Models\NotebookMember;
use App\Models\User;
use App\Notifications\NotebookSharedNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotebookMemberController extends Controller
{
    public function __construct(protected ActivityLogger $activityLogger) {}

    /**
     * Store a newly shared notebook member.
     */
    public function store(StoreNotebookMemberRequest $request, Notebook $notebook): RedirectResponse
    {
        $memberUser = User::where('email', $request->string('email')->toString())->firstOrFail();

        if ($memberUser->id === $notebook->owner_id) {
            return back()->with('status', 'The notebook owner already has full access.');
        }

        $membership = NotebookMember::updateOrCreate(
            [
                'notebook_id' => $notebook->id,
                'user_id' => $memberUser->id,
            ],
            [
                'invited_by' => $request->user()->id,
                'permission' => $request->string('permission')->toString(),
                'can_share' => $request->boolean('can_share'),
            ]
        );

        $memberUser->notify(new NotebookSharedNotification(
            $notebook,
            $request->user(),
            $membership->permission
        ));

        $this->activityLogger->log(
            $request->user(),
            'notebook.shared',
            "Shared notebook {$notebook->title} with {$memberUser->email}.",
            $notebook,
            null,
            ['permission' => $membership->permission],
            $request->ip(),
            $request->userAgent()
        );

        return back()->with('status', 'Notebook access updated.');
    }

    /**
     * Remove the specified member from the notebook.
     */
    public function destroy(Request $request, Notebook $notebook, NotebookMember $member): RedirectResponse
    {
        $this->authorize('share', $notebook);

        if ($member->notebook_id !== $notebook->id) {
            abort(404);
        }

        $email = $member->user?->email;
        $member->delete();

        $this->activityLogger->log(
            $request->user(),
            'notebook.unshared',
            "Removed notebook access for {$email}.",
            $notebook,
            null,
            ['member_email' => $email],
            $request->ip(),
            $request->userAgent()
        );

        return back()->with('status', 'Member removed from notebook.');
    }
}
