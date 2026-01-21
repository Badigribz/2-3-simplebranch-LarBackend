<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\Request;

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

    // PATCH rename
    public function update(Request $request, Person $person)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $person->update([
            'name' => $request->name,
        ]);

        return $person;
    }

    public function tree()
    {
        $root = Person::whereNull('parent_id')
            ->with('childrenRecursive')
            ->first();

        return response()->json($root);
    }

}
