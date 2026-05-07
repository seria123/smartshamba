<?php

namespace App\Services;

use App\Models\Farm;
use App\Models\Livestock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LivestockTrackingService
{
    /**
     * Generate a tracking ID in format: KE-{farm_code}-{year}-{serial}
     * KE → Country (Kenya 🇰🇪)
     * 045 → Farm or location code
     * 2024 → Year of birth or registration
     * 0012 → Animal's unique serial number
     */
    public function generateTrackingId(Livestock $livestock): string
    {
        // Get farm code (3-digit numeric code)
        $farmCode = $this->getFarmCode($livestock->farm_id);

        // Get year (from birth_date or created_at)
        $year = $this->getYear($livestock);

        // Get serial number (4-digit, zero-padded)
        $serialNumber = $this->getNextSerialNumber($farmCode, $year);

        // Format: KE-{farm_code}-{year}-{serial}
        return sprintf('KE-%03d-%04d-%04d', $farmCode, $year, $serialNumber);
    }

    /**
     * Get 3-digit farm code from farm ID
     */
    private function getFarmCode(?int $farmId): int
    {
        if (! $farmId) {
            // Default farm code for unassigned livestock
            return 999;
        }

        // Use farm ID modulo 1000 to get 3-digit code
        // Ensure it's at least 1 (000 reserved for default?)
        $code = $farmId % 1000;

        return $code === 0 ? 100 : $code; // Avoid 000
    }

    /**
     * Get year from birth_date or fallback to current year
     */
    private function getYear(Livestock $livestock): int
    {
        if ($livestock->birth_date) {
            return (int) $livestock->birth_date->format('Y');
        }

        // Fallback to year of acquisition or creation
        if ($livestock->date_acquired) {
            return (int) $livestock->date_acquired->format('Y');
        }

        // Default to current year
        return (int) now()->format('Y');
    }

    /**
     * Get next serial number for given farm code and year
     * Returns 4-digit number (0001-9999)
     */
    private function getNextSerialNumber(int $farmCode, int $year): int
    {
        // Format for matching existing tracking IDs: KE-045-2024-0012
        $pattern = sprintf('KE-%03d-%04d-%%', $farmCode, $year);

        // Find the highest serial number for this farm/year combination
        $maxSerial = Livestock::where('tracking_id', 'like', $pattern)
            ->whereNotNull('tracking_id')
            ->max(DB::raw('SUBSTRING(tracking_id, -4)'));

        // If no existing records, start at 1
        $nextSerial = $maxSerial ? ((int) $maxSerial) + 1 : 1;

        // Ensure we don't exceed 9999
        if ($nextSerial > 9999) {
            Log::warning("Serial number exceeded 9999 for farm code {$farmCode} and year {$year}");
            $nextSerial = 1; // Reset to 1 (in practice, this should trigger a new farm/code)
        }

        return $nextSerial;
    }

    /**
     * Assign tracking ID to livestock (called during creation)
     */
    public function assignTrackingId(Livestock $livestock): Livestock
    {
        // Generate tracking ID
        $trackingId = $this->generateTrackingId($livestock);

        // Ensure uniqueness (handle race condition)
        $attempts = 0;
        $maxAttempts = 5;

        while ($attempts < $maxAttempts) {
            $exists = Livestock::where('tracking_id', $trackingId)->exists();

            if (! $exists) {
                break;
            }

            // If collision occurs, increment serial and try again
            // Extract parts and increment serial
            if (preg_match('/^KE-(\d{3})-(\d{4})-(\d{4})$/', $trackingId, $matches)) {
                $farmCode = (int) $matches[1];
                $year = (int) $matches[2];
                $serial = (int) $matches[3] + 1;

                // Reset serial if it exceeds 9999
                if ($serial > 9999) {
                    $serial = 1;
                    // In practice, we might want to increment farm code or year here
                }

                $trackingId = sprintf('KE-%03d-%04d-%04d', $farmCode, $year, $serial);
            } else {
                // Fallback: just regenerate (shouldn't happen with proper format)
                $trackingId = $this->generateTrackingId($livestock);
            }

            $attempts++;
        }

        if ($attempts >= $maxAttempts) {
            // Last resort: use timestamp-based ID
            $trackingId = 'KE-999-'.now()->format('Y').'-'.str_pad(now()->timestamp % 10000, 4, '0', STR_PAD_LEFT);
            Log::warning("Failed to generate unique tracking ID after {$maxAttempts} attempts, using fallback: {$trackingId}");
        }

        // Assign tracking ID to livestock
        $livestock->tracking_id = $trackingId;
        $livestock->save();

        return $livestock;
    }
}
