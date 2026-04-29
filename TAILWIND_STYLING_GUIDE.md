# SmartShamba Tailwind Styling Guide

## Button Styles

### Primary Action Button
```html
<button class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 active:bg-emerald-900 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold text-sm">
  <i class="fas fa-plus"></i>
  <span>Button Text</span>
</button>
```

### Secondary Button (Link Style)
```html
<a href="#" class="inline-flex items-center space-x-2 px-5 py-2.5 rounded-lg border-2 border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:border-emerald-400 transition-all duration-200">
  <span>View All</span>
  <i class="fas fa-arrow-right"></i>
</a>
```

### Icon Button (Circular)
```html
<a href="#" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-emerald-200 text-emerald-600 hover:bg-emerald-50 hover:border-emerald-400 transition-all duration-200">
  <i class="fas fa-eye"></i>
</a>
```

### Danger Button
```html
<button class="inline-flex items-center space-x-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg border-2 border-red-700 hover:border-red-600 transition-all duration-200 font-semibold text-sm">
  <i class="fas fa-trash"></i>
  <span>Delete</span>
</button>
```

## Page Layout

### Page Header (Title + Action)
```html
<div class="flex items-center justify-between mb-8">
  <h1 class="text-3xl font-bold text-gray-800 tracking-wide">Page Title</h1>
  <a href="#" class="inline-flex items-center space-x-2 bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-lg border-2 border-emerald-800 hover:border-emerald-600 transition-all duration-200 shadow-sm hover:shadow-md font-semibold">
    <i class="fas fa-plus"></i>
    <span>Add New</span>
  </a>
</div>
```

### Content Container
```html
<div class="space-y-6">
  <!-- Content goes here with space-y-6 for vertical spacing -->
</div>
```

## Table Styling

### Table Container
```html
<div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
  <table class="min-w-full divide-y divide-gray-200">
    <!-- Table content -->
  </table>
</div>
```

### Table Headers
```html
<thead class="bg-gray-50">
  <tr>
    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Column Name</th>
  </tr>
</thead>
```

### Table Body Rows
```html
<tbody class="bg-white divide-y divide-gray-200">
  <tr class="hover:bg-gray-50 transition-colors duration-150">
    <td class="px-6 py-4 text-sm text-gray-700">Cell content</td>
  </tr>
</tbody>
```

## Card Styling

### Standard Card
```html
<div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm hover:shadow-md hover:border-emerald-300 transition-all duration-200 p-6">
  <!-- Card content -->
</div>
```

### Card with Header
```html
<div class="bg-white rounded-xl border-2 border-gray-200 shadow-sm overflow-hidden">
  <div class="px-6 py-4 border-b-2 border-gray-200 bg-gray-50">
    <h2 class="text-lg font-bold text-gray-800 tracking-wide">Card Title</h2>
  </div>
  <div class="p-6">
    <!-- Card content -->
  </div>
</div>
```

## Status Badges

### Green (Success/Healthy)
```html
<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
  Healthy
</span>
```

### Red (Alert/Danger)
```html
<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-300">
  Alert
</span>
```

### Yellow (Warning)
```html
<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
  Warning
</span>
```

### Gray (Inactive/Sold)
```html
<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 border border-gray-300">
  Inactive
</span>
```

## Typography

### Page Title
```html
<h1 class="text-3xl font-bold text-gray-800 tracking-wide">Page Title</h1>
```

### Section Title
```html
<h2 class="text-lg font-bold text-gray-800 tracking-wide">Section Title</h2>
```

### Subsection Title
```html
<h3 class="text-sm font-semibold text-gray-700 tracking-wide mb-3">Subsection</h3>
```

### Body Text
```html
<p class="text-sm text-gray-700 leading-relaxed">Body text goes here</p>
```

## Spacing Guidelines

- **Between sections**: `space-y-6` or `gap-6`
- **Between items in list**: `space-y-3` or `space-y-4`
- **Between button and icon**: `space-x-2`
- **Card padding**: `p-6`
- **Table cell padding**: `px-6 py-4`
- **Small gap**: `gap-3` or `space-x-2`
- **Large gap**: `gap-6` or `space-x-4`

## Color Scheme

- **Primary**: Emerald (emerald-700, emerald-800)
- **Danger**: Red (red-600, red-700)
- **Warning**: Yellow (yellow-500, yellow-600)
- **Success**: Green (green-600)
- **Info**: Blue (blue-600)
- **Neutral**: Gray

## Common Classes to Use

- Rounded corners: `rounded-lg`, `rounded-xl`
- Borders: `border-2 border-gray-200`
- Shadows: `shadow-sm`, `shadow-md`, `hover:shadow-md`
- Transitions: `transition-all duration-200`
- Hover states: Always include for interactive elements
- Text size: `text-sm`, `text-base`, `text-lg`
- Font weight: `font-semibold`, `font-bold`, `font-medium`
- Tracking (letter spacing): `tracking-wide` for headers, `tracking-wider` for labels
