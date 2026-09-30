<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWorkBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\WorkBook::class);
    }

public function rules(): array
    {
        return [
        'pic_name' => ['required', 'string', 'max:255'],
        'mitra_pengolahan' => ['required', 'string', 'max:255'],
        'kancab' => ['required', 'string', 'max:255'],
        'kanwil' => ['required', 'string', 'max:255'],
        'start_date' => ['required', 'date'],
        'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }
}
