<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PanitiaRequest extends FormRequest
{
    public function rules()
    {
        $id = $this->route('id');

        return [
            'nama' => 'required|string',
            'jabatan' => 'required|string',
            'unit' => 'required|string',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'email' => [
                'required',
                'email',
                'unique:panitias,email,' . $id,
            ],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    public function authorize()
    {
        return true;
    }
}
