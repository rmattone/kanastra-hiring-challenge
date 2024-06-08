<?php

namespace App\Http\Requests\Institutions;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstitutionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'institutionId' => ['required','int'],
            'name' => ['required','string'],
            'address' => ['nullable','string'],
            'number' => ['nullable','string'],
            'complement' => ['nullable','string'],
            'cep' => ['required','string'],
            'document' => ['nullable','string'],
            'contact' => ['nullable','array'],
            'contact.*.number' => ['nullable','string'],
            'responsable' => ['nullable','string'],
            'bankAccoundNumber' => ['nullable','string'],
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'institutionId' => (int) $this->route('institutionId'),
        ]);
    }
}
