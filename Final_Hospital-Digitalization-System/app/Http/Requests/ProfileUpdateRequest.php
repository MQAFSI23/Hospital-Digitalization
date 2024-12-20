<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:127'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:127',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'berat_badan' => ['nullable', 'numeric', 'min:0.1'],
            'tinggi_badan' => ['nullable', 'numeric', 'min:20'],
        ];
    }
}
