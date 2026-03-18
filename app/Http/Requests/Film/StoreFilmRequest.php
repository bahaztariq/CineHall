<?php

namespace App\Http\Requests;

use App\Http\Requests\AdminFormRequest;
use Illuminate\Validation\Rule;

class StoreFilmRequest extends AdminFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Inherited from AdminFormRequest.
     */
    public function authorize(): bool
    {
        return parent::authorize();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'duration'    => ['required', 'numeric', 'min:1', 'max:999'],
            'rate'        => ['required', Rule::in(['G', 'PG', 'PG-13', 'R', 'NC-17'])],
            'trailer'     => ['nullable', 'url'],
            'genres'      => ['required', 'array', 'min:1'],
            'genres.*'    => ['exists:genres,id'],
            'image'       => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required'     => 'A film title is required.',
            'description.min'    => 'The description should be at least 10 characters long.',
            'duration.numeric'   => 'The duration must be a number (minutes).',
            'rate.in'            => 'The rating must be one of: G, PG, PG-13, R, or NC-17.',
            'genres.required'    => 'Please select at least one genre for this film.',
            'genres.*.exists'    => 'One or more selected genres are invalid.',
            'image.required'     => 'A film poster image is required.',
            'image.max'          => 'The image size cannot exceed 4MB.',
            'trailer.url'        => 'The trailer link must be a valid URL.',
        ];
    }
}