# Inventory / Inputs Module — Developer Specification

## 1. Purpose of This Module

The Inventory / Inputs module manages farm stock and input movement.

This module is a backbone module because crop, livestock, irrigation, maintenance, stores, and finance workflows will eventually depend on accurate input records.

The platform should be able to answer:

- What products/items exist on the farm?
- Which warehouse/store holds them?
- What quantity is available?
- Which stock was received, issued, transferred, returned, adjusted, damaged, or expired?
- Which supplier provided the item?
- What batch/lot or expiry date applies?
- Which items are low in stock?
- Which items are expiring soon?
- Which future crop, livestock, irrigation, maintenance, or task activity consumed the item?
- What was the value/cost of the item used?

For this branch, the module should create the inventory foundation only.

It should not yet implement crop input application, livestock medicine treatment, feed consumption, irrigation fuel posting, finance cost posting, purchase approvals, supplier accounting, or sales inventory.

The goal is to create a clean stock-control foundation that future modules can safely use.

---

## 2. Current Project Position

The project already has:

- Laravel 13 foundation.
- Core/Admin module.
- Organisation and farm hierarchy.
- Sites/zones.
- Fields/blocks.
- Paddocks/pens.
- Warehouses/stores.
- Module registry.
- Users / Roles / Permissions module.
- Login/logout.
- Auth-protected admin routes.
- Role/permission foundation.
- Organisation/farm membership scoping.
- Workers / Labour module.
- Worker profiles.
- Labour teams.
- Simple attendance.
- Tasks / Work Orders module.
- Work orders.
- Tasks.
- Task assignments.
- Task updates.
- Checklist items.

The next logical step is Inventory / Inputs because future crop and livestock activities will consume stock such as seed, fertiliser, chemicals, medicine, feed, fuel, spare parts, and packaging materials.

---

## 3. Branching Note

Before starting this module, make sure the branch includes the Tasks / Work Orders work.

### If Tasks / Work Orders has been merged into main

```bash
git checkout main
git pull origin main
git checkout -b inventory-inputs-module
```

### If Tasks / Work Orders has not yet been merged

```bash
git checkout tasks-work-orders-module
git pull origin tasks-work-orders-module
git checkout -b inventory-inputs-module
```

Do not create this branch from a base that lacks Core/Admin, Users/Permissions, Workers/Labour, and Tasks/Work Orders.

---

## 4. Module Objective

The objective of this module is to create a reliable inventory foundation for farm inputs and stock movement.

The module should allow authorised users to:

1. Create product/input categories.
2. Create product/input records.
3. Manage suppliers.
4. Receive stock into warehouses/stores.
5. Issue stock out of warehouses/stores.
6. Transfer stock between warehouses/stores.
7. Return stock to stores.
8. Adjust stock with reasons.
9. Record damaged, lost, expired, or disposed stock.
10. Track batch/lot numbers and expiry dates where applicable.
11. View stock balances.
12. View stock movement history.
13. Receive low-stock and expiry visibility.
14. Prepare stock usage records for future crop, livestock, irrigation, maintenance, and finance workflows.

---

## 5. Scope of This Branch

### In scope

This branch should implement:

- Inventory module provider/routes.
- Product category model/table.
- Product/input model/table.
- Supplier model/table.
- Stock lot model/table.
- Inventory movement model/table.
- Optional stock adjustment reason structure.
- Product category list/create/view/edit/deactivate.
- Product list/create/view/edit/deactivate.
- Supplier list/create/view/edit/deactivate.
- Stock receipt flow.
- Stock issue flow.
- Stock transfer flow.
- Stock adjustment flow.
- Stock balance views.
- Stock movement history.
- Expiry and low-stock indicators.
- Auth and permission protection.
- Navigation entry.
- Seeder updates for inventory permissions.
- Local/testing demo inventory data only.
- Tests.
- README update.

### Out of scope

Do not implement:

- Crop fertiliser/chemical application logic.
- Livestock treatment or feed consumption logic.
- Irrigation fuel/energy costing.
- Finance cost posting.
- Purchase approvals.
- Supplier account balances.
- Full procurement workflow.
- Sales inventory.
- Packhouse stock.
- Barcode scanning.
- QR code labels.
- Advanced stock valuation methods.
- Payroll.
- Task-to-stock request workflow.
- Automatic stock deduction from tasks.

This branch should create inventory records and movement mechanics only.

---

## 6. Key Design Principle

Inventory is not just a product list.

The real value is stock movement history.

A product record tells us what the item is. A stock movement tells us what happened to it.

Every stock quantity change should be recorded as a movement.

Examples:

- Stock received from supplier.
- Stock issued to farm work.
- Stock transferred from Main Store to Chemical Store.
- Stock returned after partial use.
- Stock adjusted after stock count.
- Stock damaged or lost.
- Stock expired and disposed.

Do not update stock balances silently without creating a movement record.

---

## 7. Core Entities

## 7.1 Product Category

Represents a group of inventory products.

Examples:

- Seeds.
- Fertilisers.
- Chemicals.
- Veterinary medicines.
- Animal feed.
- Fuel.
- Tools.
- Spare parts.
- Packaging.
- Cleaning supplies.

### Suggested table name

```text
inventory_product_categories
```

### Suggested fields

```text
id
organization_id nullable
name
slug
description
parent_id nullable
is_system
status
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested statuses

```text
active
inactive
archived
```

### Notes

- Some categories may be system-level defaults.
- Organisation-specific categories may be added later.

---

## 7.2 Product / Input

Represents a stock item or farm input.

Examples:

- CAN fertiliser.
- DAP fertiliser.
- Tomato seed.
- Dairy meal.
- Newcastle vaccine.
- Dewormer.
- Diesel.
- Drip tape.
- Gloves.
- Egg trays.

### Suggested table name

```text
inventory_products
```

### Suggested fields

```text
id
organization_id
category_id
name
code
sku nullable
description nullable
product_type
unit_of_measure
brand nullable
manufacturer nullable
active_ingredient nullable
requires_expiry_tracking
requires_batch_tracking
reorder_level nullable
reorder_unit nullable
default_unit_cost nullable
currency nullable
storage_instructions nullable
status
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested `product_type` values

```text
seed
fertilizer
chemical
veterinary_medicine
feed
fuel
tool
spare_part
packaging
cleaning_supply
general_supply
other
```

### Suggested `unit_of_measure` examples

```text
kg
g
litre
ml
bag
packet
bottle
tablet
dose
piece
crate
tray
metre
roll
unit
```

### Suggested statuses

```text
active
inactive
archived
```

### Notes

- Product code should be unique within organisation.
- Batch tracking and expiry tracking should be available but not mandatory for all products.
- Medicine, chemicals, seeds, and feed often need batch/expiry tracking.

---

## 7.3 Supplier

Represents a vendor or source of stock.

### Suggested table name

```text
inventory_suppliers
```

### Suggested fields

```text
id
organization_id
name
code nullable
contact_person nullable
phone nullable
email nullable
address nullable
supplier_type nullable
tax_number nullable
status
notes nullable
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Suggested supplier types

```text
agrovet
feed_supplier
seed_supplier
chemical_supplier
fuel_supplier
equipment_supplier
general_supplier
other
```

---

## 7.4 Stock Lot

Represents stock for a product in a warehouse/store, optionally with batch/lot and expiry details.

### Suggested table name

```text
inventory_stock_lots
```

### Suggested fields

```text
id
organization_id
farm_id
warehouse_id
product_id
supplier_id nullable
lot_number nullable
batch_number nullable
expiry_date nullable
received_date nullable
quantity_on_hand
areserved_quantity nullable
unit_of_measure
unit_cost nullable
currency nullable
status
created_by nullable
updated_by nullable
created_at
updated_at
soft_deletes
```

### Correct typo note

Use:

```text
reserved_quantity
```

not `areserved_quantity`.

### Suggested statuses

```text
available
reserved
expired
damaged
depleted
archived
```

### Notes

- Stock balance is by product + warehouse + lot/batch where applicable.
- Do not allow quantity changes without corresponding inventory movement.

---

## 7.5 Inventory Movement

Represents every change in inventory quantity.

### Suggested table name

```text
inventory_movements
```

### Suggested fields

```text
id
organization_id
farm_id
warehouse_id nullable
from_warehouse_id nullable
to_warehouse_id nullable
product_id
stock_lot_id nullable
movement_number
movement_type
movement_date
quantity
unit_of_measure
unit_cost nullable
total_cost nullable
supplier_id nullable
reference_type nullable
reference_id nullable
reason nullable
status
performed_by nullable
approved_by nullable
approved_at nullable
notes nullable
created_at
updated_at
```

### Suggested `movement_type` values

```text
opening_balance
purchase_receipt
issue
transfer_out
transfer_in
return_to_store
adjustment_in
adjustment_out
damage
loss
expiry_disposal
correction
```

### Suggested statuses

```text
draft
posted
cancelled
reversed
```

### Notes

- For this branch, movements may be posted immediately after validation.
- Approval workflow can come later.
- `reference_type` and `reference_id` prepare for future links to tasks, crop activities, livestock treatments, irrigation events, maintenance records, and finance records.

---

## 7.6 Stock Adjustment Reason — Optional

If useful, create a table for controlled adjustment reasons.

### Suggested table name

```text
inventory_adjustment_reasons
```

### Suggested fields

```text
id
organization_id nullable
name
movement_type
description nullable
is_system
status
created_at
updated_at
```

### Examples

- Stock count correction.
- Damaged in storage.
- Expired.
- Lost/missing.
- Unit conversion correction.
- Data entry correction.

### Recommendation

This is useful but optional for the first branch. A controlled enum/reason field may be enough for now.

---

## 8. Relationships

### ProductCategory model

Should have:

```php
organization()
parent()
children()
products()
```

### Product model

Should have:

```php
organization()
category()
stockLots()
movements()
```

### Supplier model

Should have:

```php
organization()
stockLots()
movements()
```

### StockLot model

Should have:

```php
organization()
farm()
warehouse()
product()
supplier()
movements()
```

### InventoryMovement model

Should have:

```php
organization()
farm()
warehouse()
fromWarehouse()
toWarehouse()
product()
stockLot()
supplier()
performedBy()
approvedBy()
```

---

## 9. Permissions

Add starter permissions for the Inventory / Inputs module.

### Suggested permissions

```text
inventory.view
inventory.manage
products.view
products.create
products.update
products.deactivate
suppliers.view
suppliers.create
suppliers.update
suppliers.deactivate
stock.view
stock.receive
stock.issue
stock.transfer
stock.adjust
stock.manage
```

### Suggested default role assignments

Owner:

```text
all inventory permissions
```

System Admin:

```text
all inventory permissions
```

Farm Manager:

```text
inventory.view
products.view
suppliers.view
stock.view
stock.receive
stock.issue
stock.transfer
stock.adjust
```

Storekeeper:

```text
inventory.view
products.view
suppliers.view
stock.view
stock.receive
stock.issue
stock.transfer
stock.adjust
stock.manage
```

Agronomist:

```text
inventory.view
products.view
stock.view
```

Livestock Officer:

```text
inventory.view
products.view
stock.view
```

Finance Officer:

```text
inventory.view
products.view
suppliers.view
stock.view
```

Auditor:

```text
inventory.view
products.view
suppliers.view
stock.view
```

Farm Hand:

```text
none for admin inventory screens initially
```

Contractor:

```text
none initially
```

---

## 10. Routes

Suggested route prefix:

```text
/admin/inventory
```

Route names:

```text
inventory.*
```

### Suggested routes

```text
GET    /admin/inventory                                  Inventory dashboard

GET    /admin/inventory/categories                       Product category list
GET    /admin/inventory/categories/create                Create category form
POST   /admin/inventory/categories                       Store category
GET    /admin/inventory/categories/{category}            Category detail
GET    /admin/inventory/categories/{category}/edit       Edit category form
PUT    /admin/inventory/categories/{category}            Update category
POST   /admin/inventory/categories/{category}/deactivate Deactivate category

GET    /admin/inventory/products                         Product list
GET    /admin/inventory/products/create                  Create product form
POST   /admin/inventory/products                         Store product
GET    /admin/inventory/products/{product}               Product detail
GET    /admin/inventory/products/{product}/edit          Edit product form
PUT    /admin/inventory/products/{product}               Update product
POST   /admin/inventory/products/{product}/deactivate    Deactivate product

GET    /admin/inventory/suppliers                        Supplier list
GET    /admin/inventory/suppliers/create                 Create supplier form
POST   /admin/inventory/suppliers                        Store supplier
GET    /admin/inventory/suppliers/{supplier}             Supplier detail
GET    /admin/inventory/suppliers/{supplier}/edit        Edit supplier form
PUT    /admin/inventory/suppliers/{supplier}             Update supplier
POST   /admin/inventory/suppliers/{supplier}/deactivate  Deactivate supplier

GET    /admin/inventory/stock                            Stock balances
GET    /admin/inventory/movements                        Stock movement history

GET    /admin/inventory/receipts/create                  Receive stock form
POST   /admin/inventory/receipts                         Store stock receipt

GET    /admin/inventory/issues/create                    Issue stock form
POST   /admin/inventory/issues                           Store stock issue

GET    /admin/inventory/transfers/create                 Transfer stock form
POST   /admin/inventory/transfers                        Store stock transfer

GET    /admin/inventory/adjustments/create               Stock adjustment form
POST   /admin/inventory/adjustments                      Store stock adjustment
```

### Middleware

All routes should require:

```text
auth
appropriate inventory permission
```

Use the existing permission middleware pattern from Users/Permissions.

---

## 11. Controllers

Suggested controllers:

```text
InventoryDashboardController
ProductCategoryController
ProductController
SupplierController
StockBalanceController
InventoryMovementController
StockReceiptController
StockIssueController
StockTransferController
StockAdjustmentController
```

Keep controllers readable and avoid placing stock math randomly across many places.

Where possible, use a dedicated service/action for stock posting.

Suggested service/action:

```text
InventoryMovementService
```

or:

```text
PostInventoryMovementAction
```

The important rule is that all stock quantity changes should pass through one clear path.

---

## 12. Stock Movement Logic

This is the most important technical part of the module.

### 12.1 Receipt

When stock is received:

1. Validate organisation, farm, warehouse, product, quantity, unit, unit cost, supplier, batch/expiry where provided.
2. Create or find stock lot for product + warehouse + batch/lot/expiry.
3. Increase quantity_on_hand.
4. Create movement with `movement_type = purchase_receipt` or `opening_balance`.
5. Set movement status to `posted`.

### 12.2 Issue

When stock is issued:

1. Validate organisation, farm, warehouse, product, stock lot, quantity.
2. Confirm stock lot has enough quantity.
3. Decrease quantity_on_hand.
4. Create movement with `movement_type = issue`.
5. Set movement status to `posted`.

### 12.3 Transfer

When stock is transferred:

1. Validate source warehouse and destination warehouse.
2. Confirm both warehouses belong to same organisation/farm unless cross-farm transfer is explicitly allowed later.
3. Confirm source stock lot has enough quantity.
4. Decrease source stock lot.
5. Increase or create destination stock lot.
6. Create `transfer_out` movement for source.
7. Create `transfer_in` movement for destination.
8. Link the two movements if useful using reference fields or paired_transfer_id if implemented.

### 12.4 Adjustment In

When stock is increased by adjustment:

1. Validate reason.
2. Increase stock lot.
3. Create `adjustment_in` movement.

### 12.5 Adjustment Out

When stock is decreased by adjustment:

1. Validate reason.
2. Confirm enough stock where required.
3. Decrease stock lot.
4. Create `adjustment_out`, `damage`, `loss`, or `expiry_disposal` movement depending on reason.

### 12.6 Return to Store

Optional for this branch.

If implemented:

1. Increase stock lot.
2. Create `return_to_store` movement.

### 12.7 Stock should not go negative

Default rule:

```text
Do not allow negative stock.
```

Later, an admin setting may allow negative stock, but not now.

---

## 13. Validation Rules

### Product category validation

Required:

- name.
- status.

Optional:

- organization_id.
- parent_id.
- description.

### Product validation

Required:

- organization_id.
- category_id.
- name.
- code.
- product_type.
- unit_of_measure.
- status.

Rules:

- code unique within organisation.
- default_unit_cost must be numeric if provided.
- reorder_level must be numeric if provided.

### Supplier validation

Required:

- organization_id.
- name.
- status.

Optional:

- code unique within organisation if provided.
- phone.
- email.
- supplier_type.

### Stock receipt validation

Required:

- organization_id.
- farm_id.
- warehouse_id.
- product_id.
- quantity.
- unit_of_measure.
- movement_date.

Optional:

- supplier_id.
- lot_number.
- batch_number.
- expiry_date.
- unit_cost.
- notes.

Rules:

- warehouse belongs to selected farm.
- product belongs to selected organisation.
- quantity must be greater than zero.
- expiry date must be valid if provided.

### Stock issue validation

Required:

- organization_id.
- farm_id.
- warehouse_id.
- product_id.
- stock_lot_id.
- quantity.
- movement_date.

Rules:

- stock lot belongs to product and warehouse.
- quantity must be greater than zero.
- stock lot must have enough quantity.

### Stock transfer validation

Required:

- organization_id.
- farm_id.
- from_warehouse_id.
- to_warehouse_id.
- product_id.
- stock_lot_id.
- quantity.
- movement_date.

Rules:

- from and to warehouse must be different.
- both warehouses must belong to selected farm.
- stock lot must belong to source warehouse.
- stock lot must have enough quantity.

### Stock adjustment validation

Required:

- organization_id.
- farm_id.
- warehouse_id.
- product_id.
- stock_lot_id where reducing existing stock.
- movement_type.
- quantity.
- movement_date.
- reason.

Rules:

- quantity must be greater than zero.
- outgoing adjustment cannot exceed quantity on hand.

---

## 14. Views / UI

Keep UI simple and operational.

### 14.1 Inventory dashboard

Should show links/cards for:

- Products.
- Categories.
- Suppliers.
- Stock balances.
- Movements.
- Receive stock.
- Issue stock.
- Transfer stock.
- Adjust stock.

Optional simple metrics:

- Total products.
- Low stock items.
- Expiring soon.
- Recent movements.

Do not build advanced analytics yet.

### 14.2 Product category list

Columns:

- Name.
- Parent category.
- Status.
- Product count.
- Actions.

### 14.3 Product list

Columns:

- Code.
- Name.
- Category.
- Product type.
- Unit.
- Reorder level.
- Status.
- Actions.

### 14.4 Product detail

Sections:

- Product summary.
- Stock by warehouse.
- Stock lots.
- Recent movements.
- Notes.

### 14.5 Supplier list

Columns:

- Supplier code.
- Name.
- Type.
- Phone.
- Email.
- Status.
- Actions.

### 14.6 Stock balance page

Columns:

- Product.
- Warehouse.
- Lot/batch.
- Expiry date.
- Quantity on hand.
- Unit.
- Unit cost.
- Status.

Filters:

- Farm.
- Warehouse.
- Category.
- Product.
- Expiring soon.
- Low stock.

### 14.7 Stock movement history

Columns:

- Movement number.
- Date.
- Type.
- Product.
- Warehouse/from/to.
- Quantity.
- Unit.
- Performed by.
- Status.
- Notes.

### 14.8 Receive stock form

Fields:

- Organisation.
- Farm.
- Warehouse.
- Product.
- Supplier.
- Quantity.
- Unit.
- Unit cost.
- Lot number.
- Batch number.
- Expiry date.
- Movement date.
- Notes.

### 14.9 Issue stock form

Fields:

- Organisation.
- Farm.
- Warehouse.
- Product.
- Stock lot.
- Quantity.
- Movement date.
- Reference placeholder.
- Notes.

### 14.10 Transfer stock form

Fields:

- Organisation.
- Farm.
- From warehouse.
- To warehouse.
- Product.
- Stock lot.
- Quantity.
- Movement date.
- Notes.

### 14.11 Adjustment form

Fields:

- Organisation.
- Farm.
- Warehouse.
- Product.
- Stock lot.
- Adjustment type.
- Quantity.
- Reason.
- Movement date.
- Notes.

---

## 15. Navigation

Add Inventory / Inputs to the admin/module navigation if the user has relevant permissions.

Suggested label:

```text
Inventory / Inputs
```

Suggested left menu inside module:

```text
Inventory Dashboard
Products
Categories
Suppliers
Stock Balances
Movements
Receive Stock
Issue Stock
Transfer Stock
Adjust Stock
```

For this branch, a simple navigation link is enough.

Do not show inventory links to users without inventory permissions.

Backend routes must still enforce permissions.

---

## 16. Business Rules

### Rule 1: Inventory belongs to organisation and farm

Stock lots and movements must be scoped.

### Rule 2: Warehouses come from Core/Admin

Do not create a separate store table in Inventory.

Use Core/Admin warehouses/stores.

### Rule 3: Products belong to organisation

A product catalogue is organisation-level.

### Rule 4: Stock lots belong to warehouses

A product can exist without stock, but available quantity is represented through stock lots.

### Rule 5: Every stock quantity change requires a movement

Do not silently update stock lots.

### Rule 6: Stock should not go negative

Reject issues, transfers, or outgoing adjustments that exceed quantity on hand.

### Rule 7: Deactivate products, suppliers, and categories instead of deleting

Future records may reference them.

### Rule 8: Batch and expiry tracking should be supported but optional

Not every product needs batch/expiry.

### Rule 9: Demo inventory data should be local/testing only

Do not seed demo products or stock into production.

### Rule 10: Do not post finance costs yet

Unit costs may be captured, but Finance module posting is future work.

### Rule 11: Do not deduct stock from crop/livestock/task records yet

Manual stock issues are allowed. Automatic operational deductions come later.

---

## 17. Integration with Other Modules

### 17.1 Core/Admin

Inventory depends on:

- organisations,
- farms,
- warehouses/stores.

### 17.2 Users/Permissions

Inventory access depends on permissions and membership scope.

### 17.3 Tasks / Work Orders

Tasks may later request or consume stock.

Do not implement task stock requests yet.

### 17.4 Future Crops module

Crop activities will later consume inventory products such as seed, fertiliser, and chemicals.

Do not implement automatic crop stock deduction yet.

### 17.5 Future Livestock module

Livestock treatments and feeding will later consume medicines and feed.

Do not implement automatic livestock stock deduction yet.

### 17.6 Future Finance module

Inventory movements with unit cost will later support cost posting.

Do not implement finance postings yet.

---

## 18. Seed Data

Add demo inventory data only in local/testing environments.

Suggested demo data:

Categories:

```text
Seeds
Fertilisers
Chemicals
Veterinary Medicines
Animal Feed
Fuel
Tools
Packaging
```

Products:

```text
Tomato Seed
DAP Fertiliser
CAN Fertiliser
Dairy Meal
Dewormer
Diesel
```

Suppliers:

```text
Demo Agrovet Supplier
Demo Feed Supplier
```

Stock:

```text
Receive DAP Fertiliser into Main Input Store
Receive Dairy Meal into Feed Store
Receive Dewormer into Medicine Store
```

Important:

Do not seed demo inventory in production.

---

## 19. Tests Required

### Migration/model tests

- Product category can be created.
- Product can be created under organisation/category.
- Supplier can be created under organisation.
- Stock lot can be created under organisation/farm/warehouse/product.
- Inventory movement can be created.

### Route protection tests

- Guest cannot access `/admin/inventory`.
- Authenticated user without inventory permission cannot access `/admin/inventory`.
- Admin/authorised user can access `/admin/inventory`.

### CRUD tests

- Authorised user can create product category.
- Authorised user can create product.
- Authorised user can create supplier.
- Authorised user can deactivate product.
- Authorised user can deactivate supplier.

### Stock movement tests

- Receiving stock increases quantity on hand.
- Issuing stock decreases quantity on hand.
- Issue cannot exceed available quantity.
- Transfer decreases source and increases destination.
- Adjustment in increases stock.
- Adjustment out decreases stock.
- Adjustment out cannot exceed available quantity.
- Every quantity change creates movement history.

### Validation tests

- Product requires organisation/category/name/code/unit.
- Stock receipt requires warehouse/product/quantity.
- Stock issue requires stock lot and available quantity.
- Transfer requires different warehouses.
- Warehouse must belong to selected farm.

### Seeder tests

- Inventory permissions are seeded.
- Demo inventory data is only seeded in local/testing if included.

---

## 20. Acceptance Criteria

This branch is complete when:

1. Inventory module exists under `app/Modules/Inventory`.
2. Routes exist under `/admin/inventory`.
3. Inventory routes are protected by auth and permissions.
4. Product categories can be managed without hard deletes.
5. Products can be managed without hard deletes.
6. Suppliers can be managed without hard deletes.
7. Stock can be received into warehouses.
8. Stock can be issued from warehouses.
9. Stock can be transferred between warehouses.
10. Stock can be adjusted with reason.
11. Stock balances are visible.
12. Stock movement history is visible.
13. Stock cannot go negative.
14. Inventory permissions are seeded.
15. Navigation appears only for authorised users.
16. Tests pass.
17. README/module documentation is updated.
18. No crop, livestock, finance, task stock request, payroll, sales, or automatic operational stock deduction is implemented.

---

## 21. Recommended Build Order

1. Confirm branch includes Core/Admin, Users/Permissions, Workers/Labour, and Tasks.
2. Inspect current permission middleware and seeding structure.
3. Add inventory permissions to seeder.
4. Create Inventory module provider/routes.
5. Create migrations for categories, products, suppliers, stock lots, movements.
6. Create models and relationships.
7. Create inventory movement service/action.
8. Create controllers.
9. Add validation.
10. Create basic views.
11. Add navigation link with permission check.
12. Add local/testing demo seed data if useful.
13. Add tests.
14. Run validation.
15. Update README.
16. Commit.

---

## 22. Codex Prompt for Inventory / Inputs Branch

Use this prompt after confirming the branch is correctly based on Tasks / Work Orders.

```text
You are working on the `inventory-inputs-module` branch of a Laravel 13 modular monolith project for a mixed-farm management platform.

The Laravel foundation, Core/Admin module, Users/Permissions module, Workers/Labour module, and Tasks/Work Orders module already exist. Do not rebuild them.

This branch is only for the Inventory / Inputs foundation.

Do not implement crop input application, livestock treatment/feed consumption, task stock request workflow, finance/cost posting, payroll, irrigation business logic, sales, packhouse, barcode scanning, QR labels, or advanced dashboards.

First inspect the project and report:
1. Current Core/Admin models for organizations, farms, and warehouses.
2. Current Users/Permissions models, middleware, roles, permissions, and seeding pattern.
3. Current Tasks/Work Orders module state.
4. Existing app/Modules/Inventory folder state.
5. Existing navigation pattern.
6. Existing seeder pattern and local/testing demo data safeguards.
7. Any risks or conflicts before implementation.

Then implement the Inventory / Inputs module foundation:

1. Module structure:
   - Use app/Modules/Inventory.
   - Add provider/routes/controllers/models/views/tests/README as needed.

2. Migrations and models:
   - inventory_product_categories
   - inventory_products
   - inventory_suppliers
   - inventory_stock_lots
   - inventory_movements

3. Product category requirements:
   - Category can be system-level or organization-level.
   - Has name, slug, description, parent_id optional, status.
   - Deactivate by status, not hard delete.

4. Product requirements:
   - Product belongs to organization and category.
   - Has name, code, SKU optional, product_type, unit_of_measure, brand/manufacturer optional, active_ingredient optional, batch/expiry tracking flags, reorder_level, default_unit_cost, status.
   - Product code must be unique within organization.
   - Deactivate by status, not hard delete.

5. Supplier requirements:
   - Supplier belongs to organization.
   - Has name, code optional, contact info, supplier_type, status, notes.
   - Deactivate by status, not hard delete.

6. Stock lot requirements:
   - Stock lot belongs to organization, farm, warehouse, product, optional supplier.
   - Tracks lot_number, batch_number, expiry_date, quantity_on_hand, reserved_quantity, unit_of_measure, unit_cost, status.
   - Warehouse must be from Core/Admin and belong to selected farm.

7. Inventory movement requirements:
   - Every stock quantity change creates an inventory_movement.
   - Movements include movement_number, movement_type, date, product, warehouse/from/to warehouse, stock lot, quantity, unit, unit_cost, total_cost, supplier optional, reference_type/reference_id optional, reason, status, performed_by.
   - Supported movement types: opening_balance, purchase_receipt, issue, transfer_out, transfer_in, return_to_store, adjustment_in, adjustment_out, damage, loss, expiry_disposal, correction.
   - For this branch, movements can post immediately after validation.

8. Stock movement logic:
   - Receiving stock increases quantity_on_hand and creates movement.
   - Issuing stock decreases quantity_on_hand and creates movement.
   - Transfers decrease source stock and increase destination stock, creating transfer_out and transfer_in movements.
   - Adjustment in increases stock and creates movement.
   - Adjustment out/damage/loss/expiry disposal decreases stock and creates movement.
   - Do not allow negative stock.
   - Do not silently update quantity without movement.

9. Permissions:
   - Seed starter permissions:
     inventory.view, inventory.manage,
     products.view, products.create, products.update, products.deactivate,
     suppliers.view, suppliers.create, suppliers.update, suppliers.deactivate,
     stock.view, stock.receive, stock.issue, stock.transfer, stock.adjust, stock.manage.
   - Assign reasonable defaults:
     owner and system-admin get all.
     farm-manager gets starter stock/product/supplier view plus stock movement permissions.
     storekeeper gets inventory operational permissions.
     agronomist/livestock-officer/finance/auditor get view permissions.
   - Do not break existing permissions.

10. Routes:
   - Use /admin/inventory prefix.
   - Use inventory.* route names.
   - Protect all inventory routes behind auth and appropriate permissions.

11. Views:
   - Add simple Inventory dashboard.
   - Add category list/create/view/edit/deactivate.
   - Add product list/create/view/edit/deactivate.
   - Add supplier list/create/view/edit/deactivate.
   - Add stock balance page.
   - Add movement history page.
   - Add receive stock form/action.
   - Add issue stock form/action.
   - Add transfer stock form/action.
   - Add adjustment form/action.
   - Keep UI simple and consistent with existing Blade layout.

12. Navigation:
   - Add Inventory / Inputs admin navigation link only when user has inventory permissions.
   - Do not rely only on hidden links; enforce backend permission checks.

13. Demo data:
   - If demo categories/products/suppliers/stock are seeded, only create them in local/testing environments.
   - Do not seed demo inventory in production.

14. Tests:
   - Guest cannot access /admin/inventory.
   - User without inventory permission cannot access /admin/inventory.
   - Admin can access /admin/inventory.
   - Inventory permissions are seeded.
   - Product category can be created/updated/deactivated.
   - Product can be created/updated/deactivated.
   - Supplier can be created/updated/deactivated.
   - Receiving stock increases quantity and creates movement.
   - Issuing stock decreases quantity and creates movement.
   - Issue cannot exceed available quantity.
   - Transfer decreases source and increases destination and creates movement records.
   - Adjustment in/out works and creates movement records.
   - Outgoing adjustment cannot exceed quantity on hand.
   - Warehouse must belong to selected farm.

15. Documentation:
   - Update app/Modules/Inventory/README.md with entities, routes, permissions, movement rules, known limitations, and follow-up work.

Constraints:
- Do not implement crop input application.
- Do not implement livestock treatment/feed consumption.
- Do not implement task stock requests.
- Do not implement finance/cost posting.
- Do not implement payroll.
- Do not implement sales or packhouse inventory.
- Do not implement barcode/QR features.
- Do not add unnecessary packages.
- Do not commit .env, vendor, node_modules, local databases, logs, cache files, build output, or generated compiled views.

After implementation, run:
- composer install if needed
- npm install if needed
- php artisan migrate:fresh --seed
- php artisan test
- npm run build
- php artisan route:list --path=admin/inventory

Then summarize exactly what changed, what tests passed, and any risks/follow-up work.
```

---

## 23. Commit Message

If validation passes, use:

```bash
git add --dry-run .
git add .
git commit -m "Build Inventory and Inputs foundation"
git push origin inventory-inputs-module
```

---

## 24. Follow-Up After This Branch

After this module is complete, the next module should be:

```text
crops-module
```

Reason:

The Crop module can then consume inventory inputs later through controlled workflows, but for the first crop branch it should still avoid deep finance automation until costing is ready.

Known future inventory follow-ups:

- Dynamic farm/warehouse/product filtering.
- Stock request workflow from Tasks.
- Automatic deduction from crop activities.
- Automatic deduction from livestock treatments/feed records.
- Cost posting to Finance.
- Approval workflow for adjustments.
- Stock count workflow.
- Barcode/QR support.
- Supplier purchase order workflow.
- Advanced stock valuation.
- Expiry and low-stock notification engine.

