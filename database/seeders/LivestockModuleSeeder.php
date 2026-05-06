<?php

namespace Database\Seeders;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Paddock;
use App\Modules\Inventory\Models\InventoryProduct;
use App\Modules\Livestock\Models\LivestockAnimal;
use App\Modules\Livestock\Models\LivestockAnimalGroup;
use App\Modules\Livestock\Models\LivestockBreed;
use App\Modules\Livestock\Models\LivestockFeedRecord;
use App\Modules\Livestock\Models\LivestockMovementRecord;
use App\Modules\Livestock\Models\LivestockSpecies;
use App\Modules\Livestock\Models\LivestockTreatmentRecord;
use App\Modules\Livestock\Models\LivestockWeightRecord;
use App\Modules\Livestock\Models\LivestockYieldRecord;
use Illuminate\Database\Seeder;

class LivestockModuleSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $organization = Organization::query()->first();
        $farm = Farm::query()->where('organization_id', $organization?->id)->first();
        $paddock = Paddock::query()->where('farm_id', $farm?->id)->first();

        if (! $organization || ! $farm) {
            return;
        }

        $cattle = LivestockSpecies::query()->updateOrCreate(['organization_id' => null, 'code' => 'CATTLE'], ['name' => 'Cattle', 'species_type' => 'mammal', 'status' => 'active']);
        $goat = LivestockSpecies::query()->updateOrCreate(['organization_id' => null, 'code' => 'GOAT'], ['name' => 'Goat', 'species_type' => 'mammal', 'status' => 'active']);
        $poultry = LivestockSpecies::query()->updateOrCreate(['organization_id' => null, 'code' => 'POULTRY'], ['name' => 'Poultry', 'species_type' => 'bird', 'status' => 'active']);
        $fish = LivestockSpecies::query()->updateOrCreate(['organization_id' => null, 'code' => 'FISH'], ['name' => 'Fish', 'species_type' => 'fish', 'status' => 'active']);

        $friesian = LivestockBreed::query()->updateOrCreate(['species_id' => $cattle->id, 'code' => 'FRIESIAN'], ['organization_id' => null, 'name' => 'Friesian', 'status' => 'active']);
        $sahiwal = LivestockBreed::query()->updateOrCreate(['species_id' => $cattle->id, 'code' => 'SAHIWAL'], ['organization_id' => null, 'name' => 'Sahiwal', 'status' => 'active']);
        $boer = LivestockBreed::query()->updateOrCreate(['species_id' => $goat->id, 'code' => 'BOER'], ['organization_id' => null, 'name' => 'Boer Goat', 'status' => 'active']);
        $kienyeji = LivestockBreed::query()->updateOrCreate(['species_id' => $poultry->id, 'code' => 'KIENYEJI'], ['organization_id' => null, 'name' => 'Kienyeji Chicken', 'status' => 'active']);
        $broiler = LivestockBreed::query()->updateOrCreate(['species_id' => $poultry->id, 'code' => 'BROILER'], ['organization_id' => null, 'name' => 'Broiler', 'status' => 'active']);

        $cow = LivestockAnimal::query()->updateOrCreate(['farm_id' => $farm->id, 'animal_code' => 'Cow-001'], ['organization_id' => $organization->id, 'paddock_id' => $paddock?->id, 'species_id' => $cattle->id, 'breed_id' => $friesian->id, 'name' => 'Demo Friesian Cow', 'sex' => 'female', 'source' => 'purchased', 'status' => 'active', 'health_status' => 'normal', 'production_status' => 'lactating']);
        LivestockAnimal::query()->updateOrCreate(['farm_id' => $farm->id, 'animal_code' => 'Cow-002'], ['organization_id' => $organization->id, 'paddock_id' => $paddock?->id, 'species_id' => $cattle->id, 'breed_id' => $sahiwal->id, 'name' => 'Demo Sahiwal Cow', 'sex' => 'female', 'source' => 'purchased', 'status' => 'active']);
        LivestockAnimal::query()->updateOrCreate(['farm_id' => $farm->id, 'animal_code' => 'Buck-001'], ['organization_id' => $organization->id, 'paddock_id' => $paddock?->id, 'species_id' => $goat->id, 'breed_id' => $boer->id, 'name' => 'Demo Boer Buck', 'sex' => 'male', 'source' => 'purchased', 'status' => 'active']);

        $group = LivestockAnimalGroup::query()->updateOrCreate(['farm_id' => $farm->id, 'group_code' => 'Broiler Batch 001'], ['organization_id' => $organization->id, 'paddock_id' => $paddock?->id, 'species_id' => $poultry->id, 'breed_id' => $broiler->id, 'name' => 'Broiler Batch 001', 'group_type' => 'batch', 'initial_count' => 100, 'current_count' => 100, 'source' => 'purchased', 'status' => 'active']);
        LivestockAnimalGroup::query()->updateOrCreate(['farm_id' => $farm->id, 'group_code' => 'Layers Flock A'], ['organization_id' => $organization->id, 'paddock_id' => $paddock?->id, 'species_id' => $poultry->id, 'breed_id' => $kienyeji->id, 'name' => 'Layers Flock A', 'group_type' => 'flock', 'initial_count' => 50, 'current_count' => 50, 'source' => 'purchased', 'status' => 'active']);
        LivestockAnimalGroup::query()->updateOrCreate(['farm_id' => $farm->id, 'group_code' => 'Fish Pond Batch A'], ['organization_id' => $organization->id, 'species_id' => $fish->id, 'name' => 'Fish Pond Batch A', 'group_type' => 'pond', 'initial_count' => 500, 'current_count' => 500, 'source' => 'purchased', 'status' => 'active']);

        $product = InventoryProduct::query()->where('organization_id', $organization->id)->first();
        LivestockTreatmentRecord::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'animal_id' => $cow->id, 'treatment_type' => 'preventive', 'treatment_date' => now()->toDateString()], ['product_id' => $product?->id, 'product_name_snapshot' => $product?->name, 'notes' => 'Local/testing demo treatment.']);
        LivestockFeedRecord::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'animal_group_id' => $group->id, 'feed_date' => now()->toDateString()], ['product_id' => $product?->id, 'feed_name_snapshot' => $product?->name, 'quantity' => 25, 'quantity_unit' => 'kg', 'notes' => 'Local/testing demo feed record.']);
        LivestockWeightRecord::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'animal_id' => $cow->id, 'weigh_date' => now()->toDateString()], ['weight' => 420, 'weight_unit' => 'kg']);
        LivestockYieldRecord::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'animal_id' => $cow->id, 'yield_date' => now()->toDateString()], ['yield_type' => 'milk', 'quantity' => 18, 'unit_of_measure' => 'litres']);
        LivestockMovementRecord::query()->firstOrCreate(['organization_id' => $organization->id, 'farm_id' => $farm->id, 'animal_id' => $cow->id, 'movement_date' => now()->toDateString()], ['from_paddock_id' => $paddock?->id, 'to_paddock_id' => $paddock?->id, 'movement_reason' => 'Local/testing demo movement.']);
    }
}
