<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $group = $this->route('group');

        return $this->user()
            && $group
            && (int) $group->owner_id === (int) $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'cover_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'members' => [
                'nullable',
                'array',
            ],

            'members.*' => [
                'integer',
                'exists:users,id',
            ],
        ];
    }
}