<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseAnalysisService
{
    protected OpenAIService $openaiService;

    public function __construct(OpenAIService $openaiService)
    {
        $this->openaiService = $openaiService;
    }

    /**
     * Main analysis entry point - stores image, runs analysis, creates record.
     */
    abstract public function analyze(UploadedFile $image, ?int $relationId = null);

    /**
     * Perform the actual domain-specific analysis.
     */
    abstract protected function performAnalysis(UploadedFile $image): array;

    /**
     * Get the Eloquent model class for this analysis type.
     */
    abstract protected function getModelClass(): string;

    /**
     * Get the foreign key name for the related entity (field_id, livestock_id, etc).
     */
    abstract protected function getRelationIdName(): string;

    /**
     * Store uploaded image to storage with standardized naming.
     */
    protected function storeImage(UploadedFile $image, string $folder): string
    {
        $filename = Str::uuid().'.'.$image->getClientOriginalExtension();

        return $image->storeAs($folder, $filename, 'public');
    }

    /**
     * Create analysis record with standardized fields.
     * Additional fields can be passed in $extra and will be merged.
     */
    protected function createRecord(array $data, ?int $relationId = null, array $extra = [])
    {
        $modelClass = $this->getModelClass();
        $relationField = $this->getRelationIdName();

        return $modelClass::create(array_merge([
            $relationField => $relationId,
            'user_id' => Auth::id(),
            'image_path' => $data['image_path'],
            'diagnosis' => $data['diagnosis'],
            'description' => $data['description'],
            'severity' => $data['severity'],
            'recommendation' => $data['recommendation'],
            'detected_issues' => $data['detected_issues'],
            'confidence_score' => $data['confidence'],
            'status' => 'analyzed',
        ], $extra));
    }

    /**
     * Handle analysis failures with consistent fallback.
     */
    protected function handleFailure(string $error = ''): array
    {
        if ($error) {
            Log::error('Analysis failed: '.$error);
        }

        return [
            'diagnosis' => 'Analysis Unavailable',
            'description' => 'The analysis service is currently unavailable. Please try again later.',
            'severity' => null,
            'recommendation' => 'Please try again later or contact support if the problem persists.',
            'detected_issues' => [],
            'confidence' => 0,
        ];
    }
}
