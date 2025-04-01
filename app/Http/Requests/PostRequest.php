<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {

        return [
            //
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10',

        ];
    }

    public function messages()
    {
        return [
            'image.required' => 'Gambar wajib diunggah.',
            'title.required' => 'Judul wajib diisi.',
            'content.required' => 'Konten wajib diisi.',
        ];
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
}
