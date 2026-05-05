<?php

namespace App\Modules\Core\Http\Controllers\Concerns;

use App\Modules\Core\Models\Farm;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

trait ManagesCoreAssets
{
    abstract protected function modelClass(): string;

    abstract protected function routeBase(): string;

    abstract protected function label(): string;

    protected function allowsArea(): bool
    {
        return false;
    }

    protected function allowsDescription(): bool
    {
        return false;
    }

    protected function allowsSite(): bool
    {
        return true;
    }

    public function index()
    {
        $model = $this->modelClass();

        return view('core::assets.index', [
            'items' => $model::query()
                ->with($this->relations())
                ->orderBy('name')
                ->paginate(20),
            'routeBase' => $this->routeBase(),
            'label' => $this->label(),
            'allowsSite' => $this->allowsSite(),
        ]);
    }

    public function create()
    {
        return view('core::assets.form', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $model = $this->modelClass();
        $item = $model::create($this->validated($request));

        return redirect()->route($this->routeBase().'.show', $item)->with('status', $this->label().' created.');
    }

    public function show(int|string $item)
    {
        $model = $this->modelClass();
        $item = $model::query()->findOrFail($item);

        return view('core::assets.show', [
            'item' => $item->load($this->relations()),
            'routeBase' => $this->routeBase(),
            'label' => $this->label(),
            'allowsArea' => $this->allowsArea(),
            'allowsDescription' => $this->allowsDescription(),
            'allowsSite' => $this->allowsSite(),
        ]);
    }

    public function edit(int|string $item)
    {
        $model = $this->modelClass();
        $item = $model::query()->findOrFail($item);

        return view('core::assets.form', $this->formData($item));
    }

    public function update(Request $request, int|string $item): RedirectResponse
    {
        $model = $this->modelClass();
        $item = $model::query()->findOrFail($item);
        $item->update($this->validated($request));

        return redirect()->route($this->routeBase().'.show', $item)->with('status', $this->label().' updated.');
    }

    private function formData(mixed $item = null): array
    {
        return [
            'item' => $item,
            'organizations' => Organization::query()->orderBy('name')->get(),
            'farms' => Farm::query()->with('organization')->orderBy('name')->get(),
            'sites' => Site::query()->with('farm')->orderBy('name')->get(),
            'routeBase' => $this->routeBase(),
            'label' => $this->label(),
            'allowsArea' => $this->allowsArea(),
            'allowsDescription' => $this->allowsDescription(),
            'allowsSite' => $this->allowsSite(),
        ];
    }

    private function validated(Request $request): array
    {
        $rules = [
            'organization_id' => ['required', 'integer', Rule::exists('organizations', 'id')],
            'farm_id' => ['required', 'integer', Rule::exists('farms', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],
        ];

        if ($this->allowsSite()) {
            $rules['site_id'] = ['nullable', 'integer', Rule::exists('sites', 'id')];
        }

        if ($this->allowsArea()) {
            $rules['area'] = ['nullable', 'numeric', 'min:0'];
            $rules['area_unit'] = ['required', 'string', Rule::in(['acres', 'hectares', 'sqm'])];
        }

        if ($this->allowsDescription()) {
            $rules['description'] = ['nullable', 'string', 'max:2000'];
        }

        $validated = $request->validate($rules);

        $farm = Farm::query()->whereKey($validated['farm_id'])->firstOrFail();
        if ((int) $farm->organization_id !== (int) $validated['organization_id']) {
            abort(422, 'The selected farm does not belong to the selected organization.');
        }

        if ($this->allowsSite() && ! empty($validated['site_id'])) {
            $site = Site::query()->whereKey($validated['site_id'])->firstOrFail();
            if ((int) $site->farm_id !== (int) $validated['farm_id']) {
                abort(422, 'The selected site does not belong to the selected farm.');
            }
        }

        return $validated;
    }

    private function relations(): array
    {
        return $this->allowsSite()
            ? ['organization', 'farm', 'site']
            : ['organization', 'farm'];
    }
}
