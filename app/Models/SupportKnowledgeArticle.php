<?php

namespace App\Models;

class SupportKnowledgeArticle
{
    public static function all(): array
    {
        return [
            [
                'title' => 'Yellowing Crop Leaves',
                'category' => 'crop',
                'tags' => ['yellow leaves', 'nitrogen', 'watering', 'fertilizer'],
                'summary' => 'Check nitrogen deficiency, overwatering, poor drainage, and recent fertilizer timing.',
                'steps' => ['Inspect soil moisture.', 'Check lower leaves first.', 'Review fertilizer records.', 'Upload a leaf photo if symptoms are spreading.'],
            ],
            [
                'title' => 'Chicken Vaccination Basics',
                'category' => 'livestock',
                'tags' => ['chicken', 'vaccine', 'newcastle', 'poultry'],
                'summary' => 'Keep vaccine cold, follow local vet schedules, and record batch/date for every flock.',
                'steps' => ['Confirm flock age.', 'Use clean water or correct route.', 'Avoid heat exposure.', 'Record vaccination in livestock care.'],
            ],
            [
                'title' => 'M-Pesa or Payment Issue',
                'category' => 'finance',
                'tags' => ['mpesa', 'payment', 'invoice', 'receipt'],
                'summary' => 'Confirm transaction code, invoice number, buyer, and payment status before opening a finance ticket.',
                'steps' => ['Attach the M-Pesa message or receipt.', 'Check buyer name.', 'Confirm invoice number.', 'Mark ticket as urgent for missing large payments.'],
            ],
            [
                'title' => 'System Login or Page Error',
                'category' => 'system_bug',
                'tags' => ['login', 'error', 'bug', 'system'],
                'summary' => 'Capture the page, error message, and time it happened so technical support can reproduce it.',
                'steps' => ['Take a screenshot.', 'Copy the error text.', 'Mention the page URL.', 'Choose Technical Support as the agent role.'],
            ],
            [
                'title' => 'Irrigation Tips',
                'category' => 'crop',
                'tags' => ['irrigation', 'water', 'dry', 'soil moisture'],
                'summary' => 'Use recent weather and field soil observations to avoid both drought stress and overwatering.',
                'steps' => ['Check weather forecast.', 'Inspect soil 5-10 cm deep.', 'Water early morning or evening.', 'Log irrigation cost if water is metered.'],
            ],
            [
                'title' => 'Disease Control First Steps',
                'category' => 'livestock',
                'tags' => ['disease', 'sick', 'wound', 'infection'],
                'summary' => 'Isolate affected animals or plants, document symptoms, and upload photos for faster diagnosis.',
                'steps' => ['Separate affected stock where possible.', 'Record onset date.', 'Upload clear photos.', 'Escalate urgent cases to a vet.'],
            ],
        ];
    }

    public static function search(?string $query = null, ?string $category = null): array
    {
        return array_values(array_filter(self::all(), function (array $article) use ($query, $category) {
            $matchesCategory = ! $category || $article['category'] === $category;
            $haystack = strtolower($article['title'].' '.$article['summary'].' '.implode(' ', $article['tags']));
            $matchesQuery = ! $query || str_contains($haystack, strtolower($query));

            return $matchesCategory && $matchesQuery;
        }));
    }
}
