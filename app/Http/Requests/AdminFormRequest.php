<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\Response;

abstract class AdminFormRequest extends FormRequest
{
    protected ?string $errorMessage = null;
    protected ?Response $response= null;

    protected function authorize(): bool
    {
        $this->response= Gate::inspect('admin');

        if($this->response->denied()) 
        {
            $this->errorMessage= $this->response->message();
            return false;
        }
        return true;
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException($this->errorMessage);
    }
}

