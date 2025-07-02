<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateSlotRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slots' => ['required', 'array'],
            'slots.*.start_time' => ['required', 'date'],
            'slots.*.end_time' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'slots.required' => 'Укажите slots',
            'slots.array' => 'slots должен быть массивом',
            'slots.*.start_time.required' => 'Укажите время начала',
            'slots.*.end_time.required' => 'Укажите время окончания',
            'slots.*.start_time.date' => 'Неверный формат времени начала',
            'slots.*.end_time.date' => 'Неверный формат времени окончания',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiResponseHelper::error(errors: $validator->errors()->toArray()));
    }
}
