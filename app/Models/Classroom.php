<?php

namespace App\Models;

use App\Enums\ClassroomGrade;
use App\Enums\ClassroomType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    /** @use HasFactory<\Database\Factories\ClassroomFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'grade',
        'teacher_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ClassroomType::class,
            'grade' => ClassroomGrade::class,
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'enrollments',
            foreignPivotKey: 'classroom_id',
            relatedPivotKey: 'student_id'
        )->withTimestamps();
    }
}
