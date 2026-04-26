<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivestockAnalysis extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'livestock_id',
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
     * Get the livestock that owns the analysis.
     */
    public function livestock()
    {
        return $this->belongsTo(Livestock::class);
    }

    /**
     * Get the user that owns the analysis.
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
     * Common livestock diseases for AI detection simulation.
     */
    public static function getCommonDiseases(): array
    {
        return [
            [
                'name' => 'Foot and Mouth Disease',
                'description' => 'Highly contagious viral disease causing fever and blisters',
                'severity' => 'high',
                'recommendation' => 'Isolate immediately, contact veterinary officer, implement biosecurity measures',
            ],
            [
                'name' => 'Anthrax',
                'description' => 'Bacterial disease causing sudden death and skin lesions',
                'severity' => 'high',
                'recommendation' => 'Do not open carcass, report to authorities, vaccinate remaining herd',
            ],
            [
                'name' => 'Bovine Tuberculosis',
                'description' => 'Chronic bacterial infection affecting respiratory system',
                'severity' => 'high',
                'recommendation' => 'Test and cull positive animals, pasteurize milk, maintain hygiene',
            ],
            [
                'name' => 'Rift Valley Fever',
                'description' => 'Viral disease transmitted by mosquitoes causing fever and abortion',
                'severity' => 'high',
                'recommendation' => 'Vaccinate livestock, use mosquito nets, avoid handling aborted material',
            ],
            [
                'name' => 'Brucellosis',
                'description' => 'Bacterial infection causing infertility and abortion',
                'severity' => 'medium',
                'recommendation' => 'Test and cull infected animals, vaccinate heifers, use protective equipment',
            ],
            [
                'name' => 'Pneumonia',
                'description' => 'Respiratory infection causing coughing and difficulty breathing',
                'severity' => 'medium',
                'recommendation' => 'Provide antibiotics, improve ventilation, reduce stress, isolate sick animals',
            ],
            [
                'name' => 'Parasitic Worms',
                'description' => 'Internal parasites causing weight loss and diarrhea',
                'severity' => 'low',
                'recommendation' => 'Administer dewormers, rotate pastures, maintain clean water sources',
            ],
            [
                'name' => 'Mastitis',
                'description' => 'Inflammation of mammary gland causing reduced milk production',
                'severity' => 'medium',
                'recommendation' => 'Milk out affected quarter, apply antibiotics, maintain udder hygiene',
            ],
            [
                'name' => 'Tick-Borne Diseases',
                'description' => 'Diseases transmitted by ticks including East Coast Fever',
                'severity' => 'high',
                'recommendation' => 'Use acaricides regularly, inspect animals daily, clear tick habitats',
            ],
            [
                'name' => 'Nutritional Deficiency',
                'description' => 'Lack of essential vitamins or minerals in diet',
                'severity' => 'low',
                'recommendation' => 'Provide balanced feed, supplement with minerals, consult nutritionist',
            ],
            [
                'name' => 'Healthy',
                'description' => 'No signs of disease detected - animal appears healthy',
                'severity' => null,
                'recommendation' => 'Continue regular monitoring and maintain good husbandry practices',
            ],
        ];
    }
}
