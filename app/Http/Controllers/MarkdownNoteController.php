<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarkdownNote;

class MarkdownNoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|file|mimetypes:text/markdown|max:2048',
        ]);
        dd($validated);
        $note = new MarkdownNote();
        $note->title = $validated['title'];
        $note->user_id = $request->user()->id;

        if ($note->save()) {
            $file = $validated['content'];
            $path = $file->store('notes');
            $note->content = $path;
            $note->update();

            return response()->json(['message' => 'Note created successfully', 'note' => $note], 201);
        } else {
            return response()->json(['message' => 'Failed to create note'], 500);
        }
    }
}
