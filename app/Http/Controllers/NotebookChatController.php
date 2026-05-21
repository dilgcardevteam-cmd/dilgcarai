<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Notebook;
use App\Services\PdfExportService;
use Illuminate\Http\Response;

class NotebookChatController extends Controller
{
    /**
     * Export the selected chat as a PDF.
     */
    public function export(Notebook $notebook, Chat $chat, PdfExportService $pdfExport): Response
    {
        abort_unless($chat->notebook_id === $notebook->id, 404);

        $chat->load('messages.user', 'notebook');

        return $pdfExport->exportChat($chat);
    }
}
