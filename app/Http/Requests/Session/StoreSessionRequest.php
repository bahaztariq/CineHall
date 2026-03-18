<?php

namespace App\Http\Requests\Session;

use App\Http\Requests\AdminFormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreSessionRequest extends AdminFormRequest
{
    public function authorize(): bool
    {
        return parent::authorize(); // Only admins allowed
    }

    public function rules(): array
    {
        return [
            'film_id'    => ['required', 'exists:films,id'],
            'room_id'    => ['required', 'exists:rooms,id'],
            'language'   => ['required', Rule::in(['arabic', 'french', 'english'])],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time'   => ['required', 'date', 'after:start_time'],
            'price'      => ['required', 'numeric', 'min:0'],
            
            'room_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $overlap = DB::table('film_sessions')
                        ->where('room_id', $value)
                        ->where(function ($query) {
                            $query->whereBetween('start_time', [$this->start_time, $this->end_time])
                                  ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                                  ->orWhere(function ($q) {
                                      $q->where('start_time', '<=', $this->start_time)
                                        ->where('end_time', '>=', $this->end_time);
                                  });
                        })
                        ->exists();

                    if ($overlap) {
                        $fail('The selected room is already booked for a session during this time range.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.after' => 'The session must be scheduled for a future time.',
            'end_time.after'   => 'The end time must be after the start time.',
            'language.in'      => 'Language must be arabic, french, or english.',
        ];
    }
}