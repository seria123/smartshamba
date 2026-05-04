Features Added
- Farm Image Upload & Gallery: multiple images, set as main, delete
- Farm Document Management: upload, view, delete documents with types (ownership, title_deed, tax_compliance, insurance, certification, other)
- Financial/Operational Setup: estimated_budget, main_purpose (commercial/subsistence)
- Staff Tracking: permanent & casual counts stored in farm_operation_details
- User Preferences: language, notification checkboxes, weather_alerts, AI recommendations toggles
- Livestock Analysis Integration: improved livestock show page with recent analyses, direct links

Technical Changes
- Database migrations: 8 new migrations adding columns/tables for images, documents, user preferences, farm fields
- Models: FarmImage, FarmDocument, updated Farm, User, Livestock, Staff
- Controllers: FarmImageController, FarmDocumentController, updated FarmController, FarmerController, LivestockController, LivestockAnalysisController, ProfileController
- Views: Updated farm create/edit/on boarding/show, livestock show/index, staff create/edit, profile preferences
- Routes: Added resources for crops, sensors, farm images/documents, profile preferences
- Layout: Added sidebar navigation improvements

Fixes
- livestock.show page was incomplete; now full details + analysis history
- Navigation to livestock analysis from animal list/profile works
- Staff counts now save correctly (removed from direct columns)
- Missing routes: crops.create, sensors.show, farms.images.store, etc.