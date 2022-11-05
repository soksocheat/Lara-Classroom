<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'classrooms';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'room_id',
        'course_program_id',
        'start_date',
        'end_date',
        'year',
        'semester',
        'status'
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    // Many2one 
    public function room() {
        return $this->belongsTo(Room::class);
    }
    public function courseProgram() {
        return $this->belongsTo(CourseProgram::class);
    }
    public function department() {
        return $this->belongsTo(Department::class);
    }

    // One2many
    public function studentGroups() {
        return $this->hasMany(StudentGroup::class);
    }

    // Many2many
    public function lecturers() {
        return $this->belongsToMany(Lecturer::class, 'assigned_lecturers')->withPivot('order');
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

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
