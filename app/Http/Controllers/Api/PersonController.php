<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonController extends Controller
{
    // GET all people
    public function index()
    {
        return Person::all();
    }

    // POST add person
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:people,id',
        ]);

        return Person::create($data);
    }
     // PATCH update person (for editing profiles)
    public function update(Request $request, Person $person)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
            'photo_path' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'death_date' => 'nullable|date',
        ]);

        $person->update($validated);

        return $person;
    }

     // POST upload photo — NEW METHOD
    public function uploadPhoto(Request $request, Person $person)
    {
        $request->validate([
            'photo' => 'required|image|max:5120', // 5MB max
        ]);

        // Delete old photo if exists
        if ($person->photo_path) {
            Storage::disk('public')->delete($person->photo_path);
        }

        // Store new photo in storage/app/public/photos
        $path = $request->file('photo')->store('photos', 'public');

        // Update person record
        $person->update(['photo_path' => $path]);

        return response()->json([
            'message' => 'Photo uploaded successfully',
            'photo_path' => $path,
            'photo_url' => asset('storage/' . $path)
        ]);
    }

    public function tree()
    {
        $root = Person::whereNull('parent_id')
            ->with('childrenRecursive')
            ->first();

        return response()->json($root);
    }
    // DELETE person (and all descendants via cascade)
    public function destroy(Person $person)
    {
        // Delete photo if exists
        if ($person->photo_path) {
            Storage::disk('public')->delete($person->photo_path);
        }

        $person->delete();

        return response()->json([
            'message' => 'Person and descendants deleted'
        ]);
    }

    public function show(Person $person)
    {
        return response()->json($person);
    }
}
