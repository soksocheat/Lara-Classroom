<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ClassroomRequest;
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
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
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
        CRUD::column('room_id');
        CRUD::column('course_program_id');
        CRUD::column('start_date');
        CRUD::column('end_date');
        CRUD::column('year');
        CRUD::column('semester');
        CRUD::column('status');

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
        CRUD::field('room_id')->type('select2')
            ->entity('room')
            ->attribute('room_number');
        CRUD::field('course_program_id')->type('select')
            ->entity('courseProgram')
            ->model('App\Models\CourseProgram')
            ->attribute('name');
        CRUD::field('start_date')->size(6);
        CRUD::field('end_date')->size(6);
        CRUD::field('year')->type('number')->size(6);
        CRUD::field('semester')->type('number')->size(6);
        CRUD::field('status')->type('enum');

        CRUD::field('lecturers')
        ->type('repeatable')
        ->fields([
            [
                'name' => 'lecturer_id',
                'type' => 'select2',
                'model' => 'App\Models\Lecturer',
                'attribute' => 'name',
                'wrapper'   => ['class' => 'form-group col-md-6']
            ],
            [
                'name' => 'order',
                'type' => 'number',
                'wrapper'   => ['class' => 'form-group col-md-6']
            ]
        ]);
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
}
