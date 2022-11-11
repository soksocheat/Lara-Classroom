<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class CourseProgram extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'course_programs';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'remark'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param  array|null  $except
     * @return static
     */
    public function replicate(array $except = null)
    {
        $clone = parent::replicate();
        $clone->name .= ' (copy)';
        return $clone;
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function classrooms() {
        return $this->hasMany(Classroom::class);
    }

    public function courses() {
        return $this->belongsToMany(Course::class, 'course_program_details')->withPivot('order');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    public function getCoursesListAttribute() {
        return json_encode($this->courses->sortBy('pivot.order')->values()->map(function($course) {
            return ['course_id' => $course->id, 'order' => $course->pivot->order];
        }));
    }

    public function getCoursesTableAttribute() {
        return json_encode($this->courses->sortBy('pivot.order')->values()->map(function($course) {
            return ['order' => $course->pivot->order, 'name' => $course->name];
        }));
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
