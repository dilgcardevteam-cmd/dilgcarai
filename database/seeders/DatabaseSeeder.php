<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\AiEmbedding;
use App\Models\Category;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Notebook;
use App\Models\NotebookMember;
use App\Models\Source;
use App\Models\User;
use App\Notifications\NotebookSharedNotification;
use App\Notifications\SourceProcessedNotification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@notegov.test'],
            [
                'name' => 'DILG Admin',
                'role' => User::ROLE_ADMIN,
                'job_title' => 'System Administrator',
                'office' => 'DILG Central Office',
                'password' => bcrypt('password'),
            ]
        );

        $user = User::firstOrCreate(
            ['email' => 'user@notegov.test'],
            [
                'name' => 'Policy Staff',
                'role' => User::ROLE_USER,
                'job_title' => 'Policy Analyst',
                'office' => 'Policy and Planning Unit',
                'password' => bcrypt('password'),
            ]
        );

        $categories = collect([
            ['name' => 'Policy Briefs', 'slug' => 'policy-briefs', 'icon' => 'scroll-text', 'color' => '#1f6feb', 'description' => 'Policy and legislative notebooks.', 'is_system' => true],
            ['name' => 'Project Governance', 'slug' => 'project-governance', 'icon' => 'briefcase-business', 'color' => '#14b8a6', 'description' => 'Project monitoring and program oversight.', 'is_system' => true],
            ['name' => 'Meeting Intelligence', 'slug' => 'meeting-intelligence', 'icon' => 'mic', 'color' => '#f59e0b', 'description' => 'Meeting summaries and action tracking.', 'is_system' => true],
            ['name' => 'Research and Compliance', 'slug' => 'research-compliance', 'icon' => 'shield-check', 'color' => '#ec4899', 'description' => 'Research, memoranda, and compliance references.', 'is_system' => true],
        ])->map(fn (array $category) => Category::firstOrCreate(
            ['slug' => $category['slug']],
            $category
        ));

        $notebook = Notebook::firstOrCreate(
            ['slug' => 'flood-resilience-coordination'],
            [
                'owner_id' => $user->id,
                'category_id' => $categories->firstWhere('slug', 'project-governance')->id,
                'title' => 'Flood Resilience Coordination',
                'icon' => 'waves',
                'cover_color' => '#1f6feb',
                'visibility' => 'shared',
                'status' => 'active',
                'shared_token' => Str::random(40),
                'summary' => 'Cross-agency notebook for risk assessments, meeting materials, action tracking, and governance recommendations related to flood preparedness.',
                'description' => 'This notebook consolidates field reports, project references, and inter-agency notes to support local resilience planning and emergency coordination.',
                'smart_tags' => ['Flood Response', 'Inter-Agency', 'Action Items', 'Budget Coordination'],
                'featured_at' => now(),
                'last_activity_at' => now(),
            ]
        );

        if ($notebook->wasRecentlyCreated) {
            NotebookMember::create([
                'notebook_id' => $notebook->id,
                'user_id' => $admin->id,
                'invited_by' => $user->id,
                'permission' => 'editor',
                'can_share' => true,
            ]);

            $sources = collect([
                [
                    'type' => 'pdf',
                    'name' => 'Provincial Risk Assessment 2026',
                    'summary' => 'The assessment identifies delayed barangay reporting, uneven resource allocation, and a lack of synchronized evacuation triggers across municipalities.',
                    'extracted_text' => 'Provincial risk assessment highlights delayed barangay reporting, uneven resource allocation, and inconsistent evacuation triggers. Recommended focus areas include budget alignment, floodgate readiness, and barangay communication escalation procedures.',
                ],
                [
                    'type' => 'docx',
                    'name' => 'Municipal Coordination Brief',
                    'summary' => 'The coordination brief proposes a unified reporting template, clearer budget accountabilities, and shared operating timelines for LGU teams.',
                    'extracted_text' => 'Municipal coordination brief recommends a unified reporting template, clearer budget accountabilities, and synchronized operating timelines for LGUs. It also recommends standard action ownership and escalation checkpoints.',
                ],
                [
                    'type' => 'video',
                    'name' => 'Inter-Agency Meeting Recording',
                    'summary' => 'Meeting discussion emphasized communication bottlenecks, public advisory timing, and the need for a concise action tracker for local implementers.',
                    'extracted_text' => 'Meeting discussion emphasized communication bottlenecks, public advisory timing, and the need for an action tracker for field implementers. Participants requested a concise brief with lead offices and deadlines.',
                ],
            ])->map(function (array $payload) use ($notebook, $user) {
                return Source::create([
                    'notebook_id' => $notebook->id,
                    'uploaded_by' => $user->id,
                    'type' => $payload['type'],
                    'name' => $payload['name'],
                    'storage_disk' => 'public',
                    'status' => 'indexed',
                    'summary' => $payload['summary'],
                    'extracted_text' => $payload['extracted_text'],
                    'metadata' => ['seeded' => true],
                    'indexed_at' => now(),
                    'last_processed_at' => now(),
                ]);
            });

            foreach ($sources as $index => $source) {
                AiEmbedding::create([
                    'notebook_id' => $notebook->id,
                    'source_id' => $source->id,
                    'chunk_index' => $index,
                    'content_hash' => sha1($source->extracted_text),
                    'embedding_model' => null,
                    'token_count' => str_word_count($source->extracted_text),
                    'content' => $source->extracted_text,
                    'metadata' => ['source_name' => $source->name, 'type' => $source->type],
                ]);
            }

            $chat = Chat::create([
                'notebook_id' => $notebook->id,
                'user_id' => $user->id,
                'title' => 'Flood coordination briefing',
                'mode' => 'qa',
                'context_summary' => $notebook->summary,
                'last_message_at' => now(),
            ]);

            Message::create([
                'chat_id' => $chat->id,
                'user_id' => $user->id,
                'role' => 'user',
                'content' => 'Summarize the main governance issues across the uploaded sources.',
                'token_count' => 14,
            ]);

            Message::create([
                'chat_id' => $chat->id,
                'role' => 'assistant',
                'content' => 'The uploaded sources point to three consistent governance issues: delayed local reporting, unclear resource ownership, and fragmented communication workflows. A practical next step is to issue a shared action tracker with accountable offices, deadlines, and reporting checkpoints for every municipality.',
                'citations' => $sources->map(fn (Source $source) => [
                    'source_id' => $source->id,
                    'source_name' => $source->name,
                    'type' => $source->type,
                ])->values()->all(),
                'metadata' => ['provider' => 'seeded-demo'],
                'token_count' => 43,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'notebook_id' => $notebook->id,
                'action' => 'notebook.created',
                'description' => 'Created notebook Flood Resilience Coordination.',
                'properties' => ['seeded' => true],
                'created_at' => now()->subDays(2),
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'notebook_id' => $notebook->id,
                'source_id' => $sources->first()->id,
                'action' => 'source.uploaded',
                'description' => 'Uploaded source Provincial Risk Assessment 2026.',
                'properties' => ['seeded' => true],
                'created_at' => now()->subDay(),
            ]);

            ActivityLog::create([
                'user_id' => $admin->id,
                'notebook_id' => $notebook->id,
                'action' => 'notebook.shared',
                'description' => 'Shared notebook Flood Resilience Coordination with admin.',
                'properties' => ['seeded' => true],
                'created_at' => now()->subHours(10),
            ]);

            $user->notify(new SourceProcessedNotification($sources->first()));
        }
    }
}
