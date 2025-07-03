<?php

namespace App\Swagger\Requests;

use App\Helpers\ApiResponseHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateSlotRequest',
    description: 'Обновить конкретный слот',
    properties: [
        new OA\Property(
            property: 'start_time',
            type: 'string',
            format: 'date-time',
            example: '2027-06-25T12:00:00'
        ),
        new OA\Property(
            property: 'end_time',
            type: 'string',
            format: 'date-time',
            example: '2027-06-25T13:00:00'
        )
    ]

)]
class UpdateSlotRequest extends FormRequest
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
            'start_time' => ['required', 'date'],
            'end_time' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => 'Укажите время начала',
            'end_time.required' => 'Укажите время окончания',
            'start_time.date' => 'Неверный формат времени начала',
            'end_time.date' => 'Неверный формат времени окончания',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ApiResponseHelper::error(errors: $validator->errors()->toArray()));
    }
}
