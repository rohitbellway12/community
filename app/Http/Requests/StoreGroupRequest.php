<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

   public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'description' => ['nullable', 'string'],
        'cover_image' => ['nullable', 'image', 'max:2048'],
        'members' => ['nullable', 'array'],
        'members.*' => ['exists:users,id'],
    ];
}
}