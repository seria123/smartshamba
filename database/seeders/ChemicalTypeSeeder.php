<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ChemicalType;

class ChemicalTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chemicals = [
            // Insecticides
            [
                'name' => 'Imidacloprid',
                'category' => 'insecticide',
                'description' => 'Systemic, works inside the plant. Effective against aphids, caterpillars, whiteflies.',
            ],
            [
                'name' => 'Lambda-cyhalothrin',
                'category' => 'insecticide',
                'description' => 'Fast-acting contact insecticide. Effective against aphids, caterpillars, whiteflies.',
            ],
            [
                'name' => 'Cypermethrin',
                'category' => 'insecticide',
                'description' => 'Broad-spectrum insecticide. Effective against aphids, caterpillars, whiteflies.',
            ],
            [
                'name' => 'Chlorpyrifos',
                'category' => 'insecticide',
                'description' => 'Soil + foliar insects. Effective against aphids, caterpillars, whiteflies.',
            ],
            [
                'name' => 'Abamectin',
                'category' => 'insecticide',
                'description' => 'Great for mites & leaf miners.',
            ],
            [
                'name' => 'Spinosad',
                'category' => 'insecticide',
                'description' => 'Safer, more organic-friendly option. Effective against aphids, caterpillars, whiteflies.',
            ],
            // Fungicides
            [
                'name' => 'Mancozeb',
                'category' => 'fungicide',
                'description' => 'Very common, preventive. Fights fungal diseases like blight, mildew, rust.',
            ],
            [
                'name' => 'Copper oxychloride',
                'category' => 'fungicide',
                'description' => 'Bacterial + fungal control. Fights fungal diseases like blight, mildew, rust.',
            ],
            [
                'name' => 'Chlorothalonil',
                'category' => 'fungicide',
                'description' => 'Broad-spectrum protection. Fights fungal diseases like blight, mildew, rust.',
            ],
            [
                'name' => 'Metalaxyl',
                'category' => 'fungicide',
                'description' => 'For downy mildew. Fights fungal diseases like blight, mildew, rust.',
            ],
            [
                'name' => 'Propiconazole',
                'category' => 'fungicide',
                'description' => 'Systemic fungicide. Fights fungal diseases like blight, mildew, rust.',
            ],
            [
                'name' => 'Azoxystrobin',
                'category' => 'fungicide',
                'description' => 'Modern, highly effective. Fights fungal diseases like blight, mildew, rust.',
            ],
            // Herbicides
            [
                'name' => 'Glyphosate',
                'category' => 'herbicide',
                'description' => 'Non-selective (kills almost everything). Removes unwanted plants competing with your crops.',
            ],
            [
                'name' => '2,4-D',
                'category' => 'herbicide',
                'description' => 'Targets broadleaf weeds. Removes unwanted plants competing with your crops.',
            ],
            [
                'name' => 'Atrazine',
                'category' => 'herbicide',
                'description' => 'Common in maize fields. Removes unwanted plants competing with your crops.',
            ],
            [
                'name' => 'Paraquat',
                'category' => 'herbicide',
                'description' => 'Fast-acting contact herbicide (⚠️ very toxic). Removes unwanted plants competing with your crops.',
            ],
            [
                'name' => 'Pendimethalin',
                'category' => 'herbicide',
                'description' => 'Pre-emergence weed control. Removes unwanted plants competing with your crops.',
            ],
        ];

        foreach ($chemicals as $chemical) {
            ChemicalType::updateOrCreate(['name' => $chemical['name']], $chemical);
        }
    }
}
