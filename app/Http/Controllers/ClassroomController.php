<?php

namespace App\Http\Controllers;

use App\Enums\ClassroomGrade;
use App\Enums\ClassroomType;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = Classroom::with('teacher')->get();
        return response()->json($classes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'teacher')],
            'type' => ['required', Rule::enum(ClassroomType::class)],
            'grade' => ['required', Rule::enum(ClassroomGrade::class)],
        ]);

        $class = Classroom::create($validated);

        return response()->json($class, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        $class = $classroom->load(['students', 'teacher']);
        return response()->json($class);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Classroom $classroom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();
        return response()->json(['message' => 'classroom deleted']);
    }
}
