<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    // GET all photos for a person
    public function index(Person $person)
    {
        $photos = $person->photos()->orderBy('order')->get();

        // Add full URL to each photo
        $photos->transform(function ($photo) {
            return [
                'id' => $photo->id,
                'title' => $photo->title,
                'caption' => $photo->caption,
                'path' => $photo->path,
                'url' => asset('storage/' . $photo->path),
                'order' => $photo->order,
            ];
        });

        return response()->json($photos);
    }

    // POST upload a new photo
    public function store(Request $request, Person $person)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', // 5MB max
            'title' => 'nullable|string|max:255',
            'caption' => 'nullable|string',
        ]);

        // Store photo
        $path = $request->file('photo')->store('gallery', 'public');

        // Get next order number
        $maxOrder = $person->photos()->max('order') ?? 0;

        // Create photo record
        $photo = $person->photos()->create([
            'path' => $path,
            'title' => $request->input('title'),
            'caption' => $request->input('caption'),
            'order' => $maxOrder + 1,
        ]);

        return response()->json([
            'message' => 'Photo uploaded successfully',
            'photo' => [
                'id' => $photo->id,
                'title' => $photo->title,
                'caption' => $photo->caption,
                'path' => $photo->path,
                'url' => asset('storage/' . $photo->path),
                'order' => $photo->order,
            ]
        ]);
    }

    // DELETE a photo
    public function destroy(Photo $photo)
    {
        // Delete file from storage
        if ($photo->path) {
            Storage::disk('public')->delete($photo->path);
        }

        $photo->delete();

        return response()->json([
            'message' => 'Photo deleted successfully'
        ]);
    }
}
