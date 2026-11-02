<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StreamController extends Controller
{
    public function index()
    {
        try {
            $streams = Stream::orderBy('name')->paginate(15);
            return view('streams.index', compact('streams'));
        } catch (\Exception $e) {
            Log::error('Error fetching streams: ' . $e->getMessage());
            return back()->with('error', 'Failed to load streams.');
        }
    }

    public function create()
    {
        return view('streams.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:streams,name'],
            'code' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            Stream::create([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
            ]);

            return redirect()->route('streams.index')->with('success', 'Stream created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating stream: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create stream.');
        }
    }

    public function edit(Stream $stream)
    {
        return view('streams.form', compact('stream'));
    }

    public function update(Request $request, Stream $stream)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', 'unique:streams,name,' . $stream->id],
            'code' => ['nullable', 'string', 'max:20'],
        ]);

        try {
            $stream->update([
                'name' => $validated['name'],
                'code' => $validated['code'] ?? null,
            ]);

            return redirect()->route('streams.index')->with('success', 'Stream updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating stream: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update stream.');
        }
    }

    public function destroy(Stream $stream)
    {
        try {
            // Prevent deleting a stream if it's being used by any classes
            if ($stream->classes()->exists()) {
                return redirect()->route('streams.index')->with('error', 'Cannot delete: This stream is assigned to one or more classes.');
            }

            $stream->delete();
            return redirect()->route('streams.index')->with('success', 'Stream deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting stream: ' . $e->getMessage());
            return redirect()->route('streams.index')->with('error', 'Failed to delete stream.');
        }
    }
}