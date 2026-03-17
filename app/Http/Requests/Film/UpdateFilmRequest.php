<?php

namespace App\Http\Requests;

use App\Http\Requests\AdminFormRequest;
use Illuminate\Validation\Rule;

class UpdateFilmRequest extends AdminFormRequest
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
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'min:10'],
            'duration'    => ['sometimes', 'numeric', 'min:1', 'max:999'],
            'rate'        => ['sometimes', Rule::in(['G', 'PG', 'PG-13', 'R', 'NC-17'])],
            'trailer'     => ['nullable', 'url'],
            'genres'      => ['sometimes', 'array', 'min:1'],
            'genres.*'    => ['exists:genres,id'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:4096'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.string'       => 'The title must be a valid string.',
            'description.min'    => 'If provided, the description should be at least 10 characters.',
            'duration.numeric'   => 'The duration must be a numeric value representing minutes.',
            'rate.in'            => 'The rating must be one of: G, PG, PG-13, R, or NC-17.',
            'genres.array'       => 'The genres must be sent as an array.',
            'genres.*.exists'    => 'One or more selected genres do not exist in our records.',
            'image.image'        => 'The uploaded file must be an image.',
            'image.max'          => 'The new image poster cannot exceed 4MB.',
            'trailer.url'        => 'The trailer link must be a valid URL format.',
        ];
    }
}