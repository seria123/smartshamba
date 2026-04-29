# Component Migration Guide

This guide shows you how to migrate existing pages to use the new reusable Blade components.

## Before and After Examples

### 1. Converting Primary Buttons

#### ❌ Before (Inline Styles)
```blade
<a href="{{ route('crops.create') }}" 
   class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
    <i class="fas fa-plus"></i>
    <span>Add Crop</span>
</a>
```

#### ✅ After (Using Component)
```blade
<x-buttons.primary-button href="{{ route('crops.create') }}" icon="fa-plus">
    Add Crop
</x-buttons.primary-button>
```

**Benefit:** 1 line vs 7 lines, consistent styling, easier to maintain

---

### 2. Converting Secondary Buttons

#### ❌ Before
```blade
<a href="{{ route('alerts.index') }}" 
   class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-lg border-2 border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-emerald-400 transition-all duration-200">
    <span>View All Alerts</span>
    <i class="fas fa-arrow-right"></i>
</a>
```

#### ✅ After
```blade
<x-buttons.secondary-button href="{{ route('alerts.index') }}" icon="fa-arrow-right">
    View All Alerts
</x-buttons.secondary-button>
```

---

### 3. Converting Icon Buttons (Actions)

#### ❌ Before (Separate classes for each action)
```blade
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
    <a href="{{ route('crops.show', $crop->id) }}" class="text-primary hover:text-primary-dark mr-3">
        <i class="fas fa-eye"></i>
    </a>
    <a href="{{ route('crops.edit', $crop->id) }}" class="text-yellow-500 hover:text-yellow-700 mr-3">
        <i class="fas fa-edit"></i>
    </a>
    <form action="{{ route('crops.destroy', $crop->id) }}" method="POST" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure?')">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</td>
```

#### ✅ After (Consistent, organized)
```blade
<td class="px-6 py-4 text-sm font-medium">
    <div class="flex items-center space-x-2">
        <x-buttons.icon-button 
            href="{{ route('crops.show', $crop->id) }}"
            icon="fa-eye"
            color="emerald"
            title="View"
        />
        <x-buttons.icon-button
            href="{{ route('crops.edit', $crop->id) }}"
            icon="fa-edit"
            color="yellow"
            title="Edit"
        />
        <form action="{{ route('crops.destroy', $crop->id) }}" method="POST" class="inline">
            @csrf
            @method('DELETE')
            <x-buttons.icon-button
                type="submit"
                icon="fa-trash"
                color="red"
                title="Delete"
                onclick="return confirm('Are you sure?')"
            />
        </form>
    </div>
</td>
```

**Benefits:**
- Consistent styling across all action buttons
- Clear color semantics (emerald=view, yellow=edit, red=delete)
- Better spacing with `space-x-2`
- Easier to add tooltips with `title`

---

### 4. Converting Status Badges

#### ❌ Before (Custom styling for each status)
```blade
@if($animal->status === 'healthy')
    <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300">
        <i class="fas fa-check-circle mr-1.5"></i>Healthy
    </span>
@elseif($animal->status === 'sick')
    <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
        <i class="fas fa-exclamation-circle mr-1.5"></i>Sick
    </span>
@elseif($animal->status === 'sold')
    <span class="px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-300">
        <i class="fas fa-handshake mr-1.5"></i>Sold
    </span>
@endif
```

#### ✅ After (Single component)
```blade
<x-badges.status-badge :status="$animal->status" />
```

**Benefits:**
- 99% less code
- Automatic icon selection
- Consistent styling
- Easy to add more statuses

---

### 5. Converting Severity Badges

#### ❌ Before
```blade
@if($alert->severity === 'high')
    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-300">
        High
    </span>
@elseif($alert->severity === 'medium')
    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 border border-orange-300">
        Medium
    </span>
@else
    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 border border-green-300">
        Low
    </span>
@endif
```

#### ✅ After
```blade
<x-badges.severity-badge :severity="$alert->severity" />
```

---

### 6. Converting Count Badges

#### ❌ Before
```blade
<span class="inline-flex items-center px-3 py-1 rounded-lg bg-blue-100 text-blue-800 text-xs font-bold">
    <i class="fas fa-satellite-dish mr-1.5"></i>
    {{ $field->sensors->count() }}
</span>
```

#### ✅ After
```blade
<x-badges.count-badge 
    count="{{ $field->sensors->count() }}"
    icon="fa-satellite-dish"
    color="blue"
/>
```

---

## Step-by-Step Migration

### Step 1: Identify Components to Replace

Go through your templates and identify:
- `<a>` tags with button styling
- `<button>` tags with button styling
- Status/badge `<span>` tags

### Step 2: Choose Correct Component

**For buttons:**
- Main actions (Add, Create, Save) → `primary-button`
- Alternative actions (View All, Filter) → `secondary-button`
- Destructive actions (Delete) → `danger-button`
- Row actions (View, Edit, Delete) → `icon-button`

**For badges:**
- Livestock/System status → `status-badge`
- Alert severity → `severity-badge`
- Types/Categories → `type-badge`
- Numbers/Counts → `count-badge`
- General colored badges → `badge`

### Step 3: Replace Code

1. Remove inline Tailwind classes
2. Replace with appropriate component
3. Pass required attributes (href, icon, status, etc.)
4. Test styling

### Step 4: Organize Action Buttons

Wrap multiple icon buttons in a div with spacing:

```blade
<div class="flex items-center space-x-2">
    <!-- Multiple icon buttons here -->
</div>
```

---

## Migration Checklist

### Pages with Buttons to Update
- [ ] farms/index.blade.php
- [ ] fields/index.blade.php
- [ ] crops/index.blade.php
- [ ] livestock/index.blade.php
- [ ] sensors/index.blade.php
- [ ] feed_types/index.blade.php
- [ ] fertilizer_types/index.blade.php
- [ ] alerts/index.blade.php
- [ ] Other index/list pages

### Pages with Forms to Update
- [ ] crops/create.blade.php
- [ ] crops/edit.blade.php
- [ ] farms/create.blade.php
- [ ] farms/edit.blade.php
- [ ] Other create/edit pages

### Pages with Details to Update
- [ ] crops/show.blade.php
- [ ] farms/show.blade.php
- [ ] Other detail/show pages

---

## Common Migration Patterns

### Pattern 1: Page Header with Action Button

```blade
<!-- Before -->
<div class="flex items-center justify-between">
    <h1 class="text-3xl font-bold text-gray-800">Crops</h1>
    <a href="{{ route('crops.create') }}" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-lg font-semibold">
        <i class="fas fa-plus"></i>
        <span>Add Crop</span>
    </a>
</div>

<!-- After -->
<div class="flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-800">Crops</h1>
        <p class="text-sm text-gray-500 mt-2">Manage all crops</p>
    </div>
    <x-buttons.primary-button href="{{ route('crops.create') }}" icon="fa-plus">
        Add Crop
    </x-buttons.primary-button>
</div>
```

---

### Pattern 2: Table Row with Status and Actions

```blade
<!-- Before -->
<tr>
    <td>{{ $item->name }}</td>
    <td>
        @if($item->status === 'active')
            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
        @else
            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
        @endif
    </td>
    <td>
        <a href="{{ route('items.show', $item) }}" class="text-blue-600">View</a>
        <a href="{{ route('items.edit', $item) }}" class="text-yellow-600">Edit</a>
        <a href="#" class="text-red-600" onclick="delete('{{ $item->id }}')">Delete</a>
    </td>
</tr>

<!-- After -->
<tr class="hover:bg-gray-50">
    <td class="px-6 py-4">{{ $item->name }}</td>
    <td class="px-6 py-4">
        <x-badges.status-badge :status="$item->status" />
    </td>
    <td class="px-6 py-4">
        <div class="flex items-center space-x-2">
            <x-buttons.icon-button href="{{ route('items.show', $item) }}" icon="fa-eye" color="emerald" title="View" />
            <x-buttons.icon-button href="{{ route('items.edit', $item) }}" icon="fa-edit" color="yellow" title="Edit" />
            <x-buttons.icon-button icon="fa-trash" color="red" title="Delete" onclick="deleteItem({{ $item->id }})" />
        </div>
    </td>
</tr>
```

---

### Pattern 3: Alert/Card with Multiple Badges

```blade
<!-- Before -->
<div class="bg-white p-4 rounded border">
    <h3>{{ $alert->title }}</h3>
    <p>{{ $alert->message }}</p>
    <div class="flex gap-2 mt-3">
        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Critical</span>
        <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">High</span>
    </div>
</div>

<!-- After -->
<div class="bg-white p-6 rounded-xl border-2 border-gray-200">
    <h3>{{ $alert->title }}</h3>
    <p>{{ $alert->message }}</p>
    <div class="flex items-center space-x-2 mt-4">
        <x-badges.type-badge type="critical" />
        <x-badges.severity-badge severity="high" />
    </div>
</div>
```

---

## Tips for Successful Migration

1. **Migrate gradually:**
   - Don't try to update all pages at once
   - Start with index/list pages
   - Then update create/edit forms
   - Finally update detail pages

2. **Test after each change:**
   ```bash
   npm run dev  # Watch for CSS changes
   ```

3. **Use Find & Replace:**
   - Find old button patterns
   - Replace with component syntax
   - Manually adjust as needed

4. **Check responsive design:**
   - Test on mobile devices
   - Ensure buttons don't wrap unexpectedly
   - Verify badge spacing

5. **Maintain consistency:**
   - Always use appropriate component for action type
   - Keep status values consistent with database
   - Use semantic colors (green=good, red=bad)

6. **Keep the example file handy:**
   - Reference `EXAMPLE_CROPS_WITH_COMPONENTS.blade.php`
   - Compare before/after styling
   - Copy patterns that work

---

## Troubleshooting

### Component not rendering
- Check component path is correct
- Verify `php artisan view:clear` cache
- Check component name spelling

### Styling looks wrong
- Compare with original inline styles
- Check for conflicting CSS
- Verify Tailwind is compiling

### Icon not showing
- Ensure Font Awesome is loaded in MainLayout.blade.php
- Check icon class name (e.g., `fa-plus` not just `plus`)
- Verify Font Awesome version matches

### Button/Badge not responsive
- Add responsive classes if needed
- Test on mobile viewport
- Check for `whitespace-nowrap` issues

---

## Summary

By using these components, you'll:
- ✅ Reduce code duplication
- ✅ Improve consistency across pages
- ✅ Make styling changes in one place
- ✅ Onboard new developers faster
- ✅ Reduce bugs and styling inconsistencies
- ✅ Make the codebase more maintainable

Start migrating pages today! 🚀

---

**Last Updated:** April 28, 2026
