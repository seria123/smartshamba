# Blade Components Quick Reference

## Button Components

```blade
<!-- Primary Button (Green) - Main actions -->
<x-buttons.primary-button href="{{ route('crops.create') }}" icon="fa-plus">
    Add Crop
</x-buttons.primary-button>

<!-- Secondary Button (Gray) - Alternative actions -->
<x-buttons.secondary-button href="{{ route('items.index') }}">
    View All
</x-buttons.secondary-button>

<!-- Danger Button (Red) - Delete/Destructive actions -->
<x-buttons.danger-button icon="fa-trash" confirm confirmMessage="Are you sure?">
    Delete
</x-buttons.danger-button>

<!-- Icon Button (Small circular) - Row actions -->
<x-buttons.icon-button href="{{ route('items.show', $item->id) }}" icon="fa-eye" color="emerald" title="View" />
<x-buttons.icon-button href="{{ route('items.edit', $item->id) }}" icon="fa-edit" color="yellow" title="Edit" />
<x-buttons.icon-button icon="fa-trash" color="red" title="Delete" onclick="deleteItem()" />

<!-- Neutral Button (Gray) - Secondary actions -->
<x-buttons.neutral-button href="{{ route('dashboard') }}" icon="fa-home">
    Back
</x-buttons.neutral-button>
```

---

## Badge Components

```blade
<!-- Status Badge - Shows status with icon -->
<x-badges.status-badge status="healthy" />
<x-badges.status-badge status="sick" />
<x-badges.status-badge status="active" />
<x-badges.status-badge status="inactive" />
<x-badges.status-badge status="sold" />
<x-badges.status-badge status="dead" />
<x-badges.status-badge status="read" />
<x-badges.status-badge status="unread" />

<!-- Severity Badge - For alert severity -->
<x-badges.severity-badge severity="high" />
<x-badges.severity-badge severity="medium" />
<x-badges.severity-badge severity="low" />
<x-badges.severity-badge severity="critical" />
<x-badges.severity-badge severity="warning" />
<x-badges.severity-badge severity="info" />

<!-- Type Badge - For categorization -->
<x-badges.type-badge type="critical" />
<x-badges.type-badge type="warning" />
<x-badges.type-badge type="info" />
<x-badges.type-badge type="success" />
<x-badges.type-badge type="error" />
<x-badges.type-badge type="pending" />

<!-- Count Badge - For numbers -->
<x-badges.count-badge count="5" icon="fa-bell" color="red" />
<x-badges.count-badge count="12" color="emerald" />

<!-- Simple Badge - Generic colored badge -->
<x-badges.badge color="emerald">kg</x-badges.badge>
<x-badges.badge color="blue">Hay</x-badges.badge>
<x-badges.badge color="orange">In Stock</x-badges.badge>
```

---

## Icon Button Colors

| Color | Usage | Class |
|-------|-------|-------|
| **emerald** | View/Show (default) | `color="emerald"` |
| **yellow** | Edit/Modify | `color="yellow"` |
| **red** | Delete/Remove | `color="red"` |
| **green** | Approve/Success | `color="green"` |
| **blue** | Info/Details | `color="blue"` |

---

## Status Badge Values

| Status | Color | Icon | Usage |
|--------|-------|------|-------|
| healthy | Green | ✓ | Livestock/System status |
| sick | Red | ⚠ | Alert/Problem |
| active | Green | ✓ | Active/Running |
| inactive | Gray | ✗ | Disabled/Off |
| sold | Gray | 🤝 | Transaction status |
| dead | Dark | ✗ | Removed/Lost |
| read | Green | ✓ | Message status |
| unread | Yellow | ✉ | New message |

---

## Common Patterns

### Table with Icon Buttons
```blade
<td class="flex items-center space-x-2">
    <x-buttons.icon-button href="{{ route('items.show', $item->id) }}" icon="fa-eye" color="emerald" title="View" />
    <x-buttons.icon-button href="{{ route('items.edit', $item->id) }}" icon="fa-edit" color="yellow" title="Edit" />
    <x-buttons.icon-button icon="fa-trash" color="red" title="Delete" />
</td>
```

### Page Header with Action Button
```blade
<div class="flex items-center justify-between">
    <h1>Title</h1>
    <x-buttons.primary-button href="{{ route('items.create') }}" icon="fa-plus">
        Add Item
    </x-buttons.primary-button>
</div>
```

### Status Badge in Table
```blade
<td>
    <x-badges.status-badge :status="$item->status" />
</td>
```

### Multiple Badges
```blade
<div class="flex items-center space-x-2">
    <x-badges.type-badge type="{{ $alert->type }}" />
    <x-badges.severity-badge severity="{{ $alert->severity }}" />
</div>
```

---

## Directory Structure

```
resources/views/components/
├── buttons/
│   ├── primary-button.blade.php      ← Main actions
│   ├── secondary-button.blade.php    ← Alternative actions
│   ├── danger-button.blade.php       ← Delete actions
│   ├── icon-button.blade.php         ← Row actions
│   └── neutral-button.blade.php      ← Secondary actions
└── badges/
    ├── status-badge.blade.php        ← Status displays
    ├── severity-badge.blade.php      ← Alert severity
    ├── type-badge.blade.php          ← Type categories
    ├── count-badge.blade.php         ← Number displays
    └── badge.blade.php               ← Simple colored badges
```

---

## Migration Examples

### Old → New

```blade
<!-- Old: Inline styling -->
<a href="{{ route('items.show', $id) }}" class="text-emerald-600 hover:text-emerald-800">
    <i class="fas fa-eye"></i>
</a>

<!-- New: Reusable component -->
<x-buttons.icon-button href="{{ route('items.show', $id) }}" icon="fa-eye" color="emerald" />

---

<!-- Old: Custom badge -->
<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
    Active
</span>

<!-- New: Reusable component -->
<x-badges.status-badge status="active" />

---

<!-- Old: Custom button -->
<a href="{{ route('items.create') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-lg font-semibold">
    <i class="fas fa-plus"></i> Add Item
</a>

<!-- New: Reusable component -->
<x-buttons.primary-button href="{{ route('items.create') }}" icon="fa-plus">
    Add Item
</x-buttons.primary-button>
```

---

## Tips

1. **Always include `title` on icon buttons** for accessibility
   ```blade
   <x-buttons.icon-button ... title="View Details" />
   ```

2. **Use semantic colors:**
   - 🟢 Green = Good/Active/Success
   - 🔴 Red = Bad/Danger/Error
   - 🟡 Yellow = Warning/Pending
   - ⚪ Gray = Inactive/Neutral

3. **Group related buttons:**
   ```blade
   <div class="flex items-center space-x-2">
       <!-- Multiple buttons -->
   </div>
   ```

4. **Use confirmation for destructive actions:**
   ```blade
   <x-buttons.danger-button confirm confirmMessage="This cannot be undone!">
       Delete Permanently
   </x-buttons.danger-button>
   ```

5. **Combine badges for better info:**
   ```blade
   <div class="flex items-center space-x-2">
       <x-badges.type-badge type="critical" />
       <x-badges.severity-badge severity="high" />
   </div>
   ```

---

**Generated:** April 28, 2026
