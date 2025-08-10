<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarkdownNote;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpSpellcheck\MisspellingFinder;
use App\MisspellingHandler\VoidHandler;
use PhpSpellcheck\Spellchecker\Aspell;
use PhpSpellcheck\TextProcessor\MarkdownRemover;

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

    public function index(Request $request)
    {
        $notes = $request->user()->markdownNotes()->get();
        
        return response()->json($notes, 200);
    }

    public function show(MarkdownNote $note)
    {
        $file_content = Storage::get($note->content);
        $html = Str::of($file_content)->markdown();
        $note->html = $html;

        return response()->json($note, 200);
    }

    public function check(MarkdownNote $note)
    {
        $misspellingFinder = new MisspellingFinder(
            Aspell::create(), // Creates aspell spellchecker pointing to "aspell" as it's binary path
            new VoidHandler(),
            new MarkdownRemover()
        );

        $mdFormattedString = Storage::get($note->content);

        $misspellings = $misspellingFinder->find($mdFormattedString, ['en_US']);

        $result = [];

        foreach ($misspellings as $misspelling) {
            $result[] = [
                'word' => $misspelling->getWord(),
                'line' => $misspelling->getLineNumber(),
                'offset' => $misspelling->getOffset(),
                'suggestions' => $misspelling->getSuggestions(),
                'context' => \PhpSpellcheck\json_encode($misspelling->getContext())
            ];
        }

        return response()->json($result, 200);
    }
}
