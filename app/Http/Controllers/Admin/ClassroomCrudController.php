<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClassroomRequest;
use App\Models\Department;
use App\Models\Room;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ClassroomCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ClassroomCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation { store as traitStore; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Classroom::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/classroom');
        CRUD::setEntityNameStrings('classroom', 'classrooms');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('room_id')->attribute('room_number')
            ->wrapper([
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('room/'.$related_key.'/show');
                },
            ]);
        CRUD::column('course_program_id')
            ->type('select')
            ->entity('courseProgram')
            ->attribute('name')
            ->model("app\Models\CourseProgram")
            ->wrapper([
                'href' => function ($crud, $column, $entry, $related_key) {
                    return backpack_url('course-program/'.$related_key.'/show');
                },
            ]);
        CRUD::column('start_date');
        CRUD::column('end_date');
        CRUD::column('batch');
        CRUD::column('year');
        CRUD::column('semester');
        CRUD::column('status');
        CRUD::column('students')->type('closure')
            ->function(function ($entry) {
                $count = $entry->studentGroups->reduce(function ($count, $group) {
                    return $count += $group->students->count();
                }, 0);
                return $count;
            })
            ->suffix(' pupils')
            ->wrapper([
                'href' => function ($crud, $column, $entry) {
                    return backpack_url('student-group?classroom_id='.$entry->id);
                },
            ]);;


        CRUD::filter('room_id')
            ->type('select2')
            ->values(function() {
                return Room::all()->keyBy('id')->pluck('room_number', 'id')->toArray();
            })
            ->whenActive(function($value) {
                $this->crud->addClause('where', 'room_id', $value);
            })->apply();
        CRUD::filter('department_id')
            ->type('select2')
            ->values(function() {
                return Department::all()->keyBy('id')->pluck('name', 'id')->toArray();
            })
            ->whenActive(function($value) {
                $this->crud->addClause('where', 'department_id', $value);
            })->apply();
        CRUD::filter('batch')
            ->type('text')
            ->whenActive(function($value) {
                $this->crud->addClause('where', 'batch', $value);
            })->apply();
        CRUD::filter('status')
            ->type('dropdown')
            ->values(function() {
                return ['inactive' => 'Inactive', 'current' => 'Current', 'future' => 'Future'];
            })
            ->whenActive(function($value) {
                $this->crud->addClause('where', 'status', $value);
            })->apply();    
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ClassroomRequest::class);

        CRUD::field('name');
        CRUD::field('department_id');
        CRUD::field('room_id')->type('select2')
            ->entity('room')
            ->attribute('room_number')
            ->options((function ($query) { return $query->where('occupied', false)->get(); }))
            ->size(3);
        CRUD::field('course_program_id')->type('select2')
            ->entity('courseProgram')
            ->model('App\Models\CourseProgram')
            ->attribute('name')
            ->size(9);
        CRUD::field('start_date')->size(6);
        CRUD::field('end_date')->size(6);
        CRUD::field('batch')->type('number')->size(4);
        CRUD::field('year')->type('number')->size(4);
        CRUD::field('semester')->type('number')->size(4);
        CRUD::field('status')->type('enum');

        CRUD::column('status');
        CRUD::field('lecturers_list')
            ->type('repeatable')
            ->fields([
                [
                    'name' => 'lecturer_id',
                    'type' => 'select2_grouped',
                    'model' => 'App\Models\Lecturer',
                    'entity' => 'lecturers',
                    'attribute' => 'name',
                    'group_by' => 'department',
                    'group_by_attribute' => 'name',
                    'group_by_relationship_back' => 'lecturers',
                    'wrapper'   => ['class' => 'form-group col-md-6']
                ],
                [
                    'name' => 'order',
                    'type' => 'number',
                    'wrapper'   => ['class' => 'form-group col-md-6']
                ],
            ])
            ->init_rows(5)
            ->min_rows(5)
            ->max_rows(5);
            
        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function store()
    {
        $lecturers = collect(json_decode(request('lecturers_list'), true));

        $response = $this->traitStore();
        
        $this->crud->entry->lecturers()->sync($lecturers);

        return $response;
    }

    public function update()
    {
        $lecturers = collect(json_decode(request('lecturers_list'), true));

        $response = $this->traitUpdate();
        
        $this->crud->entry->lecturers()->detach();
        $this->crud->entry->lecturers()->attach($lecturers);

        return $response;
    }

    protected function setupShowOperation() {
        CRUD::column('name');
        CRUD::column('department_id')->type('relationship');
        CRUD::column('course_program_id')->type('closure')
            ->function(function ($entry) {
                return $entry->courseProgram->name;
            });
        CRUD::column('start_date')->type('date');
        CRUD::column('end_date')->type('date');
        CRUD::column('batch');
        CRUD::column('year');
        CRUD::column('semester');
        CRUD::column('status');

        CRUD::column('lecturers_courses_table')->type('table')
            ->label('Courses & Lecturers')
            ->columns([
                'Mon' => 'Mon',
                'Tue' => 'Tue',
                'Wed' => 'Wed',
                'Thu' => 'Thu',
                'Fri' => 'Fri',
            ]);
        CRUD::column('morning_students_table')->type('table')
            ->label('Morning Students')
            ->columns([
                'count' => 'Count',
                'name' => 'Name',
                'birthdate' => 'Birthdate'
            ]);
        CRUD::column('afternoon_students_table')->type('table')
            ->label('Afternoon Students')
            ->columns([
                'count' => 'Count',
                'name' => 'Name',
                'birthdate' => 'Birthdate'
            ]);
        CRUD::column('evening_students_table')->type('table')
            ->label('Evening Students')
            ->columns([
                'count' => 'Count',
                'name' => 'Name',
                'birthdate' => 'Birthdate'
            ]);
    }
}
