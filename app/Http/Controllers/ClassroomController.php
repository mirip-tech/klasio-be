<?php

namespace App\Http\Controllers;

use App\Enums\ClassroomGrade;
use App\Enums\ClassroomType;
use App\Enums\Role;
use App\Models\Classroom;
use App\Rules\MemberOfTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
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
            'name' => ['required', 'string', 'max:255'],
            'teacher_id' => ['nullable', 'integer:strict', Rule::exists('users', 'id'), new MemberOfTenant(Role::TEACHER)],
            'type' => ['required', Rule::enum(ClassroomType::class)],
            'grade' => ['required', Rule::enum(ClassroomGrade::class)],
        ]);

        $class = DB::transaction(fn() => Classroom::create($validated));

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
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'teacher_id' => ['sometimes', 'nullable', 'integer:strict', Rule::exists('users', 'id'), new MemberOfTenant(Role::TEACHER)],
            'type' => ['sometimes', 'required', Rule::enum(ClassroomType::class)],
            'grade' => ['sometimes', 'required', Rule::enum(ClassroomGrade::class)],
        ]);

        DB::transaction(fn() => $classroom->update($validated));

        return response()->json($classroom->fresh());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Classroom $classroom)
    {
        DB::transaction(fn() => $classroom->delete());

        return response()->json(['message' => 'classroom deleted']);
    }

    /**
     * Enroll collection of student
     */
    public function enroll(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(fn() => $classroom->students()->syncWithoutDetaching($validated['student_ids']));

        return response()->json(['message' => 'students enrolled successfully']);
    }
}
