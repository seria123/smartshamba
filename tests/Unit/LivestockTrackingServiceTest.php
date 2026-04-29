<?php

namespace Tests\Unit;

use App\Models\Livestock;
use App\Models\Farm;
use App\Models\User;
use App\Models\LivestockType;
use App\Services\LivestockTrackingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivestockTrackingServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var LivestockTrackingService */
    protected $trackingService;

    /** @var User */
    protected $user;

    /** @var Farm */
    protected $farm;

    /** @var LivestockType */
    protected $livestockType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trackingService = new LivestockTrackingService();

        // Create test user
        $this->user = User::factory()->create();

        // Create test farm
        $this->farm = Farm::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create test livestock type
        $this->livestockType = LivestockType::factory()->create();
    }

    /** @test */
    public function it_generates_correct_tracking_id_format()
    {
        // Create livestock with known birth date
        $livestock = Livestock::factory()->make([
            'user_id' => $this->user->id,
            'livestock_type_id' => $this->livestockType->id,
            'farm_id' => $this->farm->id,
            'birth_date' => '2024-05-15', // Year 2024
        ]);

        $trackingId = $this->trackingService->generateTrackingId($livestock);

        // Should match format: KE-{farm_code}-{year}-{serial}
        $this->assertMatchesRegularExpression(
            '/^KE-\d{3}-\d{4}-\d{4}$/',
            $trackingId
        );

        // Year should be 2024 from birth_date
        $this->assertStringContainsString('-2024-', $trackingId);
    }

    /** @test */
    public function it_uses_current_year_when_no_birth_date()
    {
        // Create livestock without birth date
        $livestock = Livestock::factory()->make([
            'user_id' => $this->user->id,
            'livestock_type_id' => $this->livestockType->id,
            'farm_id' => $this->farm->id,
            'birth_date' => null,
        ]);

        $trackingId = $this->trackingService->generateTrackingId($livestock);

        // Should contain current year
        $currentYear = now()->format('Y');
        $this->assertStringContainsString("-{$currentYear}-", $trackingId);
    }

    /** @test */
    public function it_assigns_unique_tracking_ids()
    {
        // Create first livestock
        $livestock1 = Livestock::factory()->create([
            'user_id' => $this->user->id,
            'livestock_type_id' => $this->livestockType->id,
            'farm_id' => $this->farm->id,
            'birth_date' => '2024-01-01',
        ]);

        $this->trackingService->assignTrackingId($livestock1);

        // Create second livestock
        $livestock2 = Livestock::factory()->create([
            'user_id' => $this->user->id,
            'livestock_type_id' => $this->livestockType->id,
            'farm_id' => $this->farm->id,
            'birth_date' => '2024-01-01',
        ]);

        $this->trackingService->assignTrackingId($livestock2);

        // Refresh from database
        $livestock1->refresh();
        $livestock2->refresh();

        // Tracking IDs should be different
        $this->assertNotEquals(
            $livestock1->tracking_id,
            $livestock2->tracking_id
        );

        // Both should follow the format
        $this->assertMatchesRegularExpression(
            '/^KE-\d{3}-\d{4}-\d{4}$/',
            $livestock1->tracking_id
        );
        $this->assertMatchesRegularExpression(
            '/^KE-\d{3}-\d{4}-\d{4}$/',
            $livestock2->tracking_id
        );
    }

    /** @test */
    public function it_handles_livestock_without_farm()
    {
        // Create livestock without farm
        $livestock = Livestock::factory()->make([
            'user_id' => $this->user->id,
            'livestock_type_id' => $this->livestockType->id,
            'farm_id' => null,
            'birth_date' => '2024-01-01',
        ]);

        $trackingId = $this->trackingService->generateTrackingId($livestock);

        // Should use default farm code (999)
        $this->assertStringContainsString('KE-999-', $trackingId);
    }

    /** @test */
    public function it_increments_serial_number_for_same_farm_and_year()
    {
        $farm = Farm::factory()->create([
            'user_id' => $this->user->id,
        ]);

        // Create first livestock
        $livestock1 = Livestock::factory()->create([
            'user_id' => $this->user->id,
            'livestock_type_id' => $this->livestockType->id,
            'farm_id' => $farm->id,
            'birth_date' => '2024-01-01',
        ]);

        $this->trackingService->assignTrackingId($livestock1);

        // Create second livestock with same farm and year
        $livestock2 = Livestock::factory()->create([
            'user_id' => $this->user->id,
            'livestock_type_id' => $this->livestockType->id,
            'farm_id' => $farm->id,
            'birth_date' => '2024-01-01',
        ]);

        $this->trackingService->assignTrackingId($livestock2);

        // Refresh from database
        $livestock1->refresh();
        $livestock2->refresh();

        // Extract serial numbers (last 4 digits)
        preg_match('/-(\d{4})$/', $livestock1->tracking_id, $matches1);
        preg_match('/-(\d{4})$/', $livestock2->tracking_id, $matches2);

        $serial1 = (int)$matches1[1];
        $serial2 = (int)$matches2[1];

        // Second serial should be first serial + 1
        $this->assertEquals($serial1 + 1, $serial2);
    }
}