<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\AdminFormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomRequest extends AdminFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return parent::authorize(); // Restricted to admins via AdminFormRequest
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'     => ['sometimes', 'string', 'max:255', Rule::unique('rooms')->ignore($this->room)],
            'type'     => ['sometimes', Rule::in(['Normal', 'VIP'])],
            'capacity' => ['sometimes', 'integer', 'min:1'],
        ];
    }


    public function messages(): array
    {
        return [
            'name.string'      => 'The room name must be a valid text string.',
            'name.unique'      => 'This room name is already taken by another room.',
            'type.in'          => 'The room type must be either "Normal" or "VIP".',
            'capacity.integer' => 'The capacity must be a whole number.',
            'capacity.min'     => 'The room must be able to hold at least 1 person.',
        ];
    }
}
