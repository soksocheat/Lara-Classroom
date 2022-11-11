<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class StudentGroup extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'student_groups';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'name',
        'batch',
        'year',
        'semester',
        'department_id',
        'classroom_id',
        'shift_id',
        'active'
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
    // Many2one
    public function department() {
        return $this->belongsTo(Department::class);
    }
    public function shift() {
        return $this->belongsTo(Shift::class);
    }
    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }

    // Many2many
    public function students() {
        return $this->belongsToMany(Student::class, 'student_group_details');
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
    public function getStudentsTableAttribute() {
        // call values() to reset the index (0-1-2...) after sorting 
        return json_encode($this->students->sortBy('name')->values()->map(function($student, $key) {
            return ['count' => $key + 1, 'name' => $student->name, 'birthdate' => $student->birthdate];
        }));
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
