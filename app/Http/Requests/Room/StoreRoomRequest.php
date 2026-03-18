<?php

namespace App\Http\Requests\Room;

use App\Http\Requests\AdminFormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends AdminFormRequest
{
    public function authorize(): bool
    {
        return parent::authorize(); // Only admins allowed
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255', 'unique:rooms,name'],
            'type'     => ['required', Rule::in(['Normal', 'VIP'])],
            'capacity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique'      => 'A room with this name already exists.',
            'type.in'          => 'Room type must be either Normal or VIP.',
            'capacity.integer' => 'Capacity must be a whole number.',
        ];
    }
}