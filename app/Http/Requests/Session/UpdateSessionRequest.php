<?php

namespace App\Http\Requests;

use App\Http\Requests\AdminFormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UpdateSessionRequest extends AdminFormRequest
{
    public function authorize(): bool
    {
        return parent::authorize();
    }

    public function rules(): array
    {

        $startTime = $this->start_time ?? $this->route('film_session')->start_time;
        $endTime = $this->end_time ?? $this->route('film_session')->end_time;
        $roomId = $this->room_id ?? $this->route('film_session')->room_id;

        return [
            'film_id'    => ['sometimes', 'exists:films,id'],
            'room_id'    => ['sometimes', 'exists:rooms,id'],
            'language'   => ['sometimes', Rule::in(['arabic', 'french', 'english'])],
            'start_time' => ['sometimes', 'date', 'after:now'],
            'end_time'   => ['sometimes', 'date', 'after:start_time'],
            'price'      => ['sometimes', 'numeric', 'min:0'],
            'room_id' => [
                'sometimes',
                function ($attribute, $value, $fail) use ($startTime, $endTime, $roomId) {
                    $overlap = DB::table('film_sessions')
                        ->where('room_id', $roomId)
                        ->where('id', '!=', $this->route('film_session')->id) 
                        ->where(function ($query) use ($startTime, $endTime) {
                            $query->whereBetween('start_time', [$startTime, $endTime])
                                  ->orWhereBetween('end_time', [$startTime, $endTime])
                                  ->orWhere(function ($q) use ($startTime, $endTime) {
                                      $q->where('start_time', '<=', $startTime)
                                        ->where('end_time', '>=', $endTime);
                                  });
                        })
                        ->exists();

                    if ($overlap) {
                        $fail('The room is unavailable during the updated time range.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'price.numeric' => 'The price must be a valid decimal number.',
        ];
    }
}