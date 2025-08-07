<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarkdownNote;
use Illuminate\Support\Str;

class MarkdownNoteController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|file|extensions:md|max:2048',
        ]);

        $note = new MarkdownNote();
        $note->title = $validated['title'];
        $note->user_id = $request->user()->id;

        if ($note->save()) {
            $filename = $note->user_id . '-' . $note->id . '-' . Str::slug($note->title, '-');
            $file = $validated['content'];
            $path = $file->storeAs('notes', $filename . '.md');
            $note->content = $path;
            $note->update();

            return response()->json(['message' => 'Note created successfully', 'note' => $note], 201);
        } else {
            return response()->json(['message' => 'Failed to create note'], 500);
        }
    }
}
