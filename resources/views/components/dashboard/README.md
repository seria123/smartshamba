# SmartShamba Dashboard Components

## Overview
Mobile-first responsive dashboard optimized for agricultural use in rural conditions.

## Components

### 1. Summary Bar (`x-dashboard.summary-bar`)
**Location:** `resources/views/components/dashboard/summary-bar.blade.php`

Sticky top bar showing quick status overview:
- Weather (temp + condition)
- Alerts count
- Crop health status
- Water status
- Livestock alerts

Props: `weather`, `alerts-count`, `crop-health`, `water-status`, `livestock-alerts`

### 2. Status Card (`x-dashboard.card`)
**Location:** `resources/views/components/dashboard/card.blade.php`

Main dashboard card with icon, status indicator, and metric:

Props:
- `icon` (string) - Font Awesome icon class
- `title` (string) - Card title
- `status` (string) - good/warning/fair/poor/critical
- `metric` (string) - Numeric value
- `metric-label` (string) - Unit/label
- `href` (string) - Navigation URL
- `color` (string) - green/blue/yellow/brown/red

### 3. Status Indicator (`x-dashboard.status-indicator`)
**Location:** `resources/views/components/dashboard/status-indicator.blade.php`

Reusable colored status dot:
- Green: excellent/good
- Yellow: warning/fair
- Orange: poor
- Red: critical

Props: `status`, `size` (sm/md/lg)

### 4. Insight (`x-dashboard.insight`)
**Location:** `resources/views/components/dashboard/insight.blade.php`

AI insights display with type-based styling:
- `recommendation` - Green, AI suggestions
- `warning` - Yellow, risk alerts
- `task` - Blue, upcoming tasks
- `irrigation` - Blue water icon
- `info` - Gray, general info

Props: `type`, `priority` (high/medium/low)

### 5. Bottom Navigation (`x-dashboard.bottom-nav`)
**Location:** `resources/views/components/dashboard/bottom-nav.blade.php`

Mobile-only bottom nav (hidden on lg+ screens):
- Home, Crops, Livestock, Insights, Settings

Props: `active` (current route key)

## Color Palette
```css
Green:   #1B5E20 (primary - crops/health)
Brown:   #6D4C41 (secondary - soil/earth)
Yellow:  #F9A825 (warning - alerts/harvest)
Blue:    #0288D1 (water/weather)
Red:     #D32F2F (critical - alerts/disease)
Background: #F5F7F2 (soft off-white)
```

## Responsive Grid
| Breakpoint | Columns | Nav      |
|------------|---------|----------|
| < 640px    | 1       | Bottom   |
| 640-1023px | 2       | Bottom   |
| 1024px+    | 3-4     | Sidebar  |

## Usage Example
```blade
<x-layout.app-layout title="Dashboard">
    <x-dashboard.bottom-nav active="home" />
    
    <x-dashboard.summary-bar 
        :weather="['temperature' => '24°C', 'condition' => 'Sunny']"
        :alerts-count="3"
        crop-health="good"
        water-status="adequate"
        :livestock-alerts="1"
    />
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <x-dashboard.card icon="fa-seedling" title="Crops" status="good" 
            metric="12" metric-label="Active" href="#" color="green" />
    </div>
</x-layout.app-layout>
```

## Accessibility
- Minimum 48px touch targets
- WCAG AA contrast ratios
- Semantic HTML
- Dark mode support