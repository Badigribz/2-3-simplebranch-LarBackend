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

    // GET single person
    public function show(Person $person)
    {
        return response()->json($person);
    }

    // POST add person
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:people,id',
        ]);

        $user = auth()->user();

        // If user is editor, enforce they can only add children to themselves
        if ($user->isEditor() && !$user->isAdmin()) {
            if ($data['parent_id'] !== $user->person_id) {
                abort(403, 'Editors can only add children to their own profile');
            }
        }

        return Person::create($data);
    }

    // PATCH update person
    public function update(Request $request, Person $person)
    {
        // Policy already checked via middleware

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

    // POST upload profile photo
    public function uploadPhoto(Request $request, Person $person)
    {
        // Policy already checked via middleware

        $request->validate([
            'photo' => 'required|image|max:5120', // 5MB max
        ]);

        // Delete old photo if exists
        if ($person->photo_path) {
            Storage::disk('public')->delete($person->photo_path);
        }

        // Store new photo
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

    // DELETE person
    public function destroy(Person $person)
    {
        // Policy already checked via middleware

        // Delete photo if exists
        if ($person->photo_path) {
            Storage::disk('public')->delete($person->photo_path);
        }

        // Delete all photos in gallery
        foreach ($person->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }

        $person->delete();

        return response()->json([
            'message' => 'Person and descendants deleted'
        ]);
    }
}
