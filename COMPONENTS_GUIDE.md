# Blade Components Usage Guide

This guide shows how to use the reusable Blade components for buttons and badges throughout the SmartShamba application.

## Button Components

### 1. Primary Button
Used for main actions like "Add", "Create", "Save", etc.

```blade
<!-- As a link -->
<x-buttons.primary-button href="{{ route('crops.create') }}" icon="fa-plus">
    Add Crop
</x-buttons.primary-button>

<!-- As a button -->
<x-buttons.primary-button type="submit" icon="fa-save">
    Save Changes
</x-buttons.primary-button>

<!-- Without icon -->
<x-buttons.primary-button href="{{ route('dashboard') }}">
    Go to Dashboard
</x-buttons.primary-button>
```

**Features:**
- Emerald green background with hover effects
- Optional icon from Font Awesome
- Works as link (`href`) or button
- Shadow effects on hover
- Smooth transitions

---

### 2. Secondary Button
Used for less important actions like "View All", "View", etc.

```blade
<!-- As a link -->
<x-buttons.secondary-button href="{{ route('alerts.index') }}" icon="fa-arrow-right">
    View All
</x-buttons.secondary-button>

<!-- As a button -->
<x-buttons.secondary-button type="submit">
    Filter Results
</x-buttons.secondary-button>
```

**Features:**
- Light gray background with border
- Hover effect changes to emerald accent
- Good for secondary actions
- Less prominent than primary button

---

### 3. Danger Button
Used for destructive actions like "Delete", "Remove", etc.

```blade
<!-- Simple delete button -->
<x-buttons.danger-button icon="fa-trash" confirm confirmMessage="Are you sure you want to delete this crop?">
    Delete
</x-buttons.danger-button>

<!-- In a form -->
<form action="{{ route('crops.destroy', $crop->id) }}" method="POST">
    @csrf
    @method('DELETE')
    <x-buttons.danger-button type="submit" icon="fa-trash" confirm confirmMessage="This action cannot be undone. Continue?">
        Delete Crop
    </x-buttons.danger-button>
</form>
```

**Properties:**
- `icon` - Font Awesome icon class
- `confirm` - Show confirmation dialog (boolean)
- `confirmMessage` - Custom confirmation message
- `type` - button type (default: "button")

---

### 4. Icon Button (Small Circular)
Used for individual row actions (View, Edit, Delete).

```blade
<!-- View button -->
<x-buttons.icon-button 
    href="{{ route('crops.show', $crop->id) }}"
    icon="fa-eye"
    color="emerald"
    title="View"
/>

<!-- Edit button -->
<x-buttons.icon-button
    href="{{ route('crops.edit', $crop->id) }}"
    icon="fa-edit"
    color="yellow"
    title="Edit"
/>

<!-- Delete button -->
<x-buttons.icon-button
    icon="fa-trash"
    color="red"
    title="Delete"
    @click="deleteItem()"
/>
```

**Properties:**
- `icon` - Font Awesome icon (required)
- `color` - Color variant: emerald, yellow, red, green, blue, default (default: emerald)
- `title` - Tooltip text
- `href` - Link (optional, uses `<a>` tag if provided)
- Works as button or link

**Color Variants:**
```
emerald: Green (for view/show actions)
yellow:  Yellow (for edit actions)
red:     Red (for delete actions)
green:   Green (for approve/success actions)
blue:    Blue (for info/details)
```

---

### 5. Neutral Button
Used for secondary gray actions.

```blade
<x-buttons.neutral-button href="{{ route('dashboard') }}" icon="fa-home">
    Back to Dashboard
</x-buttons.neutral-button>
```

---

## Badge Components

### 1. Status Badge
Shows status with color-coded styling and icons.

```blade
<!-- Livestock status -->
<x-badges.status-badge status="healthy">
    Healthy
</x-badges.status-badge>

<x-badges.status-badge status="sick">
    Sick
</x-badges.status-badge>

<x-badges.status-badge status="sold" />

<!-- Without custom text (uses status as display) -->
<x-badges.status-badge status="active" />

<!-- With custom icon -->
<x-badges.status-badge status="healthy" icon="fa-heartbeat">
    Perfect Health
</x-badges.status-badge>
```

**Supported Statuses:**
- healthy (Green with check icon)
- sick (Red with alert icon)
- active (Green with check icon)
- inactive (Gray with times icon)
- sold (Gray with handshake icon)
- dead (Dark with times icon)
- read (Green with check icon)
- unread (Yellow with envelope icon)

---

### 2. Severity Badge
Used for alert severity levels.

```blade
<x-badges.severity-badge severity="high">
    High Priority
</x-badges.severity-badge>

<x-badges.severity-badge severity="medium" />

<x-badges.severity-badge severity="low">
    Low Risk
</x-badges.severity-badge>
```

**Supported Levels:**
- high (Red)
- medium (Orange)
- low (Green)
- critical (Red)
- warning (Yellow)
- info (Blue)

---

### 3. Type Badge
Used for categorizing types (alerts, statuses, etc.).

```blade
<x-badges.type-badge type="critical">
    Critical Alert
</x-badges.type-badge>

<x-badges.type-badge type="warning" />

<x-badges.type-badge type="info">
    Information
</x-badges.type-badge>
```

**Supported Types:**
- critical (Red)
- warning (Yellow)
- info (Blue)
- success (Green)
- error (Red)
- pending (Gray)

---

### 4. Count Badge
Used for showing numbers with optional icon.

```blade
<!-- Sensor count -->
<x-badges.count-badge 
    count="{{ $field->sensors->count() }}"
    icon="fa-satellite-dish"
    color="blue"
/>

<!-- Alert count -->
<x-badges.count-badge 
    count="5"
    icon="fa-bell"
    color="red"
/>

<!-- Without icon -->
<x-badges.count-badge count="12" color="emerald" />
```

**Color Options:**
- emerald (default)
- red
- yellow
- green
- blue
- gray

---

### 5. Simple Badge
Generic colored badge for any text.

```blade
<!-- Unit badge -->
<x-badges.badge color="emerald">
    kg
</x-badges.badge>

<!-- Type badge -->
<x-badges.badge color="blue">
    Hay
</x-badges.badge>

<!-- Custom -->
<x-badges.badge color="orange">
    In Stock
</x-badges.badge>
```

**Available Colors:**
- emerald, red, yellow, green, blue, orange, purple, pink, gray

---

## Complete Examples

### Table with Actions

```blade
<table class="min-w-full">
    <thead class="bg-gray-50">
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($crops as $crop)
            <tr>
                <td>{{ $crop->name }}</td>
                <td>
                    <x-badges.status-badge status="active" />
                </td>
                <td>
                    <x-badges.badge color="emerald">
                        {{ $crop->type }}
                    </x-badges.badge>
                </td>
                <td>
                    <div class="flex items-center space-x-2">
                        <x-buttons.icon-button 
                            href="{{ route('crops.show', $crop->id) }}"
                            icon="fa-eye"
                            title="View"
                        />
                        <x-buttons.icon-button
                            href="{{ route('crops.edit', $crop->id) }}"
                            icon="fa-edit"
                            color="yellow"
                            title="Edit"
                        />
                        <x-buttons.icon-button
                            icon="fa-trash"
                            color="red"
                            title="Delete"
                            @click="deleteItem({{ $crop->id }})"
                        />
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
```

### Page Header with Buttons

```blade
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Crops</h1>
        <p class="text-sm text-gray-500 mt-2">Manage all your crop plantings</p>
    </div>
    <x-buttons.primary-button 
        href="{{ route('crops.create') }}"
        icon="fa-plus"
    >
        Add Crop
    </x-buttons.primary-button>
</div>
```

### Alert List with Badges

```blade
@foreach($alerts as $alert)
    <div class="bg-white p-6 rounded-lg border mb-4">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <h3 class="font-bold text-gray-900">{{ $alert->title }}</h3>
                <p class="text-gray-600 mt-2">{{ $alert->message }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <x-badges.type-badge type="{{ $alert->type }}" />
                <x-badges.severity-badge severity="{{ $alert->severity }}" />
            </div>
        </div>
        <div class="flex items-center justify-between mt-4 pt-4 border-t">
            <x-badges.status-badge :status="$alert->is_read ? 'read' : 'unread'" />
            <div class="flex items-center space-x-2">
                <x-buttons.icon-button
                    href="{{ route('alerts.show', $alert->id) }}"
                    icon="fa-eye"
                    title="View"
                />
                <x-buttons.icon-button
                    href="{{ route('alerts.edit', $alert->id) }}"
                    icon="fa-edit"
                    color="yellow"
                    title="Edit"
                />
                <x-buttons.icon-button
                    icon="fa-trash"
                    color="red"
                    title="Delete"
                />
            </div>
        </div>
    </div>
@endforeach
```

### Statistics Card Section

```blade
<div class="grid grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-lg border">
        <p class="text-gray-500 text-sm font-bold">Total Crops</p>
        <p class="text-3xl font-bold mt-2">{{ $totalCrops }}</p>
        <x-badges.count-badge 
            count="{{ $activeCrops }}"
            color="green"
            class="mt-4"
        >
            Active
        </x-badges.count-badge>
    </div>
    <!-- More cards -->
</div>
```

---

## Migration Guide

### Converting Old Code

**Before:**
```blade
<a href="{{ route('crops.show', $crop->id) }}" class="text-primary hover:text-primary-dark mr-3">
    <i class="fas fa-eye"></i>
</a>
```

**After:**
```blade
<x-buttons.icon-button 
    href="{{ route('crops.show', $crop->id) }}"
    icon="fa-eye"
    color="emerald"
    title="View"
/>
```

---

**Before:**
```blade
<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
    {{ $status }}
</span>
```

**After:**
```blade
<x-badges.status-badge :status="$status" />
```

---

**Before:**
```blade
<a href="{{ route('crops.create') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition">
    <i class="fas fa-plus mr-2"></i>Add Crop
</a>
```

**After:**
```blade
<x-buttons.primary-button href="{{ route('crops.create') }}" icon="fa-plus">
    Add Crop
</x-buttons.primary-button>
```

---

## Best Practices

1. **Use appropriate button types:**
   - Primary for main actions
   - Secondary for alternative actions
   - Danger for destructive actions
   - Icon buttons for row actions

2. **Keep badge meanings consistent:**
   - Green = Success/Active/Healthy
   - Red = Danger/Critical/Error
   - Yellow/Orange = Warning
   - Gray = Inactive/Neutral
   - Blue = Info

3. **Always include titles on icon buttons:**
   ```blade
   <x-buttons.icon-button ... title="View Details" />
   ```

4. **Use semantic HTML:**
   - Use `<a>` for navigation (href property)
   - Use `<button>` for form submissions
   - Use proper color semantics

5. **Group related icon buttons:**
   ```blade
   <div class="flex items-center space-x-2">
       <!-- Multiple icon buttons -->
   </div>
   ```

6. **Include confirmation dialogs for destructive actions:**
   ```blade
   <x-buttons.danger-button 
       icon="fa-trash"
       confirm
       confirmMessage="This action cannot be undone. Continue?"
   >
       Delete
   </x-buttons.danger-button>
   ```

---

## Component Properties Reference

### Primary Button
- `href` (optional) - Makes it a link
- `icon` (optional) - Font Awesome class
- `type` (default: "button") - button type
- Any HTML attributes via `$attributes`

### Icon Button
- `href` (optional) - Makes it a link
- `icon` (required) - Font Awesome class
- `color` - emerald|yellow|red|green|blue|default
- `title` - Tooltip text
- `type` (default: "button") - button type

### Danger Button
- `confirm` (boolean) - Show confirmation
- `confirmMessage` - Custom confirmation text
- Other properties same as primary button

### Status Badge
- `status` (required) - Status value
- `icon` (optional) - Custom Font Awesome icon
- `$slot` (optional) - Custom display text

### Severity Badge
- `severity` (required) - Severity level
- `$slot` (optional) - Custom display text

### Type Badge
- `type` (required) - Type value
- `icon` (optional) - Custom icon
- `$slot` (optional) - Custom text

### Count Badge
- `count` (required) - Number to display
- `icon` (optional) - Font Awesome class
- `color` - emerald|red|yellow|green|blue|gray

### Badge (Simple)
- `color` - Tailwind color variant
- `$slot` - Badge text
- Any HTML attributes

---

**Last Updated:** April 28, 2026
