<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseProgramRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }
    
    protected function prepareForValidation()
    {
        $this->merge([
            'courses_list' => json_decode($this->input('courses_list'), true)
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 'name' => 'required|min:5|max:255'
            'name' => ['required', Rule::unique('course_programs', 'name')->ignore($this->id)],
            'remark' => 'nullable',
            'courses_list.*.course_id' => 'required|distinct',
            'courses_list.*.order' => 'required|distinct|numeric|between:1,5'
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'courses_list.*.course_id.distinct' => 'Please check that there are no duplicate courses.',
            'courses_list.*.order.distinct' => 'Please check that there is no duplicate order.',
            'courses_list.*.order.between' => 'Order must be between 1 and 5.'
        ];
    }
}
