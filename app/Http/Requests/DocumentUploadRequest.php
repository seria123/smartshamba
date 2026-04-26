<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx|max:10240',
            'title' => 'required|string|max:255',
            'document_type' => [
                'required',
                'string',
                Rule::in([
                    'national_id',
                    'land_title',
                    'farm_registration',
                    'certificate',
                    'tax_document',
                    'insurance',
                    'loan_document',
                    'other',
                ]),
            ],
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'document.required' => 'Please select a document to upload',
            'document.file' => 'Please upload a valid file',
            'document.mimes' => 'File must be PDF, JPG, PNG, GIF, DOC, or XLS',
            'document.max' => 'File size must not exceed 10MB',
            'title.required' => 'Document title is required',
            'document_type.required' => 'Document type is required',
            'document_type.in' => 'Invalid document type selected',
        ];
    }
}
