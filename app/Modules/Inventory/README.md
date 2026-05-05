# Inventory / Inputs Module

This module provides the foundation for farm inputs and stock control. It manages product categories, products, suppliers, stock lots, and movement history.

It intentionally does not implement crop input application, livestock treatment/feed consumption, task stock requests, finance/cost posting, payroll, sales, packhouse flows, barcode/QR features, or advanced dashboards.

## Entities

- `inventory_product_categories`: system-level or organization-level product categories with optional parent category and status deactivation.
- `inventory_products`: organization-scoped input/product records with category, product code, SKU, type, unit, tracking flags, reorder level, default cost, and status.
- `inventory_suppliers`: organization-scoped suppliers with contact fields, supplier type, notes, and status.
- `inventory_stock_lots`: organization/farm/warehouse scoped stock balances for one product and optional supplier.
- `inventory_movements`: immutable stock movement records for receipts, issues, transfers, returns, adjustments, damage, loss, expiry disposal, and corrections.

## Routes

All routes use `/admin/inventory`, `inventory.*` names, `auth`, and permission middleware.

- Dashboard: `inventory.dashboard`
- Categories: list/create/show/edit/deactivate
- Products: list/create/show/edit/deactivate
- Suppliers: list/create/show/edit/deactivate
- Stock balances: `inventory.stock.balances`
- Movement history: `inventory.movements.index`
- Receive stock: `inventory.stock.receive`
- Issue stock: `inventory.stock.issue`
- Transfer stock: `inventory.stock.transfer`
- Adjust stock: `inventory.stock.adjust`

## Movement Rules

- Receiving stock creates a stock lot, increases `quantity_on_hand`, and records an inventory movement.
- Issuing stock decreases `quantity_on_hand` and records an inventory movement.
- Transfers decrease the source lot, create a destination lot, and record `transfer_out` and `transfer_in` movements.
- Adjustment in increases stock and records a movement.
- Adjustment out, damage, loss, and expiry disposal decrease stock and record movements.
- Negative stock is blocked.
- Quantity changes should not be made without an `inventory_movements` record.

## Permissions

Seeded permissions:

- `inventory.view`, `inventory.manage`
- `products.view`, `products.create`, `products.update`, `products.deactivate`
- `suppliers.view`, `suppliers.create`, `suppliers.update`, `suppliers.deactivate`
- `stock.view`, `stock.receive`, `stock.issue`, `stock.transfer`, `stock.adjust`, `stock.manage`

Default assignments:

- `owner` and `system-admin`: all permissions.
- `farm-manager`: starter inventory management permissions.
- `storekeeper`: operational inventory permissions.
- `agronomist`, `livestock-officer`, `finance-officer`, and `auditor`: view permissions.

## Demo Data

`InventoryInputsSeeder` creates demo categories, products, suppliers, stock lots, and opening movement only in `local` and `testing` environments.

## Known Limitations

- No crop, livestock, task, finance, payroll, sales, packhouse, barcode, or QR integrations.
- Transfers create a new destination lot rather than merging lots.
- Forms use simple select lists instead of dynamic farm-aware filtering.
- Movement posting is immediate after validation; approval workflows can be added later.
