<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CropAnalysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'field_id',
        'user_id',
        'image_path',
        'diagnosis',
        'description',
        'severity',
        'recommendation',
        'detected_issues',
        'confidence_score',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'detected_issues' => 'array',
        'confidence_score' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the field that owns the crop analysis.
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the user that owns the crop analysis.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get severity color for display.
     */
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get status color for display.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warning',
            'analyzed' => 'info',
            'reviewed' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Common plant diseases for AI detection simulation.
     */
    public static function getCommonDiseases(): array
    {
        return [
            [
                'name' => 'Leaf Blight',
                'description' => 'Fungal infection causing brown spots on leaves',
                'severity' => 'high',
                'recommendation' => 'Apply fungicide and remove affected leaves',
            ],
            [
                'name' => 'Powdery Mildew',
                'description' => 'White powdery coating on leaves',
                'severity' => 'medium',
                'recommendation' => 'Improve air circulation and apply sulfur-based fungicide',
            ],
            [
                'name' => 'Whiteflies',
                'description' => 'White flying insects that weaken plants by sucking sap',
                'severity' => 'medium',
                'recommendation' => 'Use sticky traps and insecticidal soap',
            ],
            [
                'name' => 'Bacterial Spot',
                'description' => 'Dark water-soaked spots on leaves',
                'severity' => 'medium',
                'recommendation' => 'Apply copper-based bactericide',
            ],
            [
                'name' => 'Root Rot',
                'description' => 'Yellowing and wilting due to overwatering',
                'severity' => 'high',
                'recommendation' => 'Reduce watering and improve drainage',
            ],
            [
                'name' => 'Aphid Infestation',
                'description' => 'Small insects sucking sap from plants',
                'severity' => 'medium',
                'recommendation' => 'Apply insecticidal soap or neem oil',
            ],
            [
                'name' => 'Spider Mites',
                'description' => 'Tiny mites causing stippled leaves',
                'severity' => 'medium',
                'recommendation' => 'Increase humidity and apply miticide',
            ],
            [
                'name' => 'Nitrogen Deficiency',
                'description' => 'Yellowing of older leaves',
                'severity' => 'low',
                'recommendation' => 'Apply nitrogen-rich fertilizer',
            ],
            [
                'name' => 'Cutworms',
                'description' => 'Larvae that cut young plants at the base',
                'severity' => 'high',
                'recommendation' => 'Use collars around stems and apply biological pesticides',
            ],
            [
                'name' => 'Thrips',
                'description' => 'Tiny insects that scrape plant surfaces causing silvery patches',
                'severity' => 'medium',
                'recommendation' => 'Use blue sticky traps and apply insecticidal soap',
            ],
            [
                'name' => 'Leaf Miners',
                'description' => 'Larvae that create winding tunnels inside leaves',
                'severity' => 'low',
                'recommendation' => 'Remove affected leaves and use neem oil',
            ],
            [
                'name' => 'Armyworms',
                'description' => 'Caterpillars that rapidly consume leaves in large numbers',
                'severity' => 'high',
                'recommendation' => 'Apply biological pesticides like Bacillus thuringiensis (Bt)',
            ],
            [
                'name' => 'Mealybugs',
                'description' => 'White cotton-like insects that suck plant sap',
                'severity' => 'medium',
                'recommendation' => 'Wipe with alcohol or apply neem oil',
            ],
            [
                'name' => 'Grasshoppers',
                'description' => 'Chewing insects that eat large portions of leaves',
                'severity' => 'medium',
                'recommendation' => 'Use netting or organic repellents',
            ],
            [
                'name' => 'Downy Mildew',
                'description' => 'Yellow patches on leaves with gray mold underneath',
                'severity' => 'medium',
                'recommendation' => 'Improve airflow and apply fungicide',
            ],
            [
                'name' => 'Fusarium Wilt',
                'description' => 'Soil-borne fungus causing wilting and yellowing',
                'severity' => 'high',
                'recommendation' => 'Remove infected plants and use resistant varieties',
            ],
            [
                'name' => 'Bacterial Wilt',
                'description' => 'Sudden wilting caused by bacterial infection',
                'severity' => 'high',
                'recommendation' => 'Control pests and remove infected plants',
            ],
            [
                'name' => 'Rust',
                'description' => 'Orange or brown powdery spots on leaves',
                'severity' => 'medium',
                'recommendation' => 'Apply fungicide and remove infected leaves',
            ],
            [
                'name' => 'Damping Off',
                'description' => 'Seedlings collapse due to fungal infection in soil',
                'severity' => 'high',
                'recommendation' => 'Use sterile soil and avoid overwatering',
            ],
            [
                'name' => 'Healthy',
                'description' => 'No issues detected - plant is healthy',
                'severity' => null,
                'recommendation' => 'Continue regular maintenance',
            ],
        ];
    }
}
