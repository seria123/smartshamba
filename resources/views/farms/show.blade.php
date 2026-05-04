@extends('layouts.MainLayout')

@section('title', 'Farm Details - SmartShamba')

@section('content')
<div class="space-y-6">
     <!-- Page Header -->
     <div class="flex items-center justify-between">
         <h1 class="text-3xl font-bold text-gray-800">{{ $farm->name }}</h1>
         <div class="flex space-x-2">
             <a href="{{ route('farms.edit', $farm->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
                 <i class="fas fa-edit mr-2"></i>Edit
             </a>
             <a href="{{ route('farms.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                 <i class="fas fa-arrow-left mr-2"></i>Back
             </a>
         </div>
     </div>

      <!-- Farm Details -->
      <div class="bg-white rounded-lg shadow-md p-6">
          <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
              <div>
                  <p class="text-sm text-gray-500">Location (County)</p>
                  <p class="text-lg font-medium text-gray-800">{{ $farm->location ?? 'N/A' }}</p>
              </div>
              <div>
                  <p class="text-sm text-gray-500">Subcounty</p>
                  <p class="text-lg font-medium text-gray-800">{{ $farm->subcounty ?? 'N/A' }}</p>
              </div>
              <div>
                  <p class="text-sm text-gray-500">Farm Type</p>
                  <p class="text-lg font-medium text-gray-800">
                      {{ ucfirst($farm->farm_type ?? 'N/A') }}
                  </p>
              </div>
              <div>
                  <p class="text-sm text-gray-500">Ownership</p>
                  <p class="text-lg font-medium text-gray-800">
                      {{ ucfirst($farm->ownership_type ?? 'N/A') }}
                  </p>
              </div>
              <div>
                  <p class="text-sm text-gray-500">Size</p>
                  <p class="text-lg font-medium text-gray-800">{{ $farm->size_hectares ? $farm->size_hectares . ' ha' : 'N/A' }}</p>
              </div>
          </div>
           <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
               <div>
                   <p class="text-sm text-gray-500">Physical Address</p>
                   <p class="text-gray-800">{{ $farm->physical_address ?? 'N/A' }}</p>
               </div>
               <div>
                   <p class="text-sm text-gray-500">Storage Facilities</p>
                   <p class="text-lg font-medium text-gray-800">
                       {{ $farm->storage_facilities ? '✅ Yes' : '❌ No' }}
                   </p>
               </div>
               <div>
                   <p class="text-sm text-gray-500">Estimated Budget</p>
                   <p class="text-lg font-medium text-gray-800">
                       {{ $farm->estimated_budget ? 'KES ' . number_format($farm->estimated_budget, 2) : 'N/A' }}
                   </p>
               </div>
               <div>
                   <p class="text-sm text-gray-500">Main Purpose</p>
                   <p class="text-lg font-medium text-gray-800">
                       {{ $farm->main_purpose ? ucfirst($farm->main_purpose) : 'N/A' }}
                   </p>
               </div>
           </div>
          @if($farm->description)
              <div class="mt-6">
                  <p class="text-sm text-gray-500">Description</p>
                  <p class="text-gray-800">{{ $farm->description }}</p>
              </div>
              @endif
      @if($farm->latitude && $farm->longitude)
              <div class="mt-4">
                  <p class="text-sm text-gray-500">GPS Coordinates</p>
                  <p class="text-gray-800">
                      {{ $farm->latitude }}, {{ $farm->longitude }}
                      <a href="https://www.google.com/maps?q={{ $farm->latitude }},{{ $farm->longitude }}" target="_blank" class="text-primary hover:underline ml-2">
                          <i class="fas fa-external-link-alt"></i> View on Map
                      </a>
                  </p>
              </div>
          @endif
      </div>

       <!-- Staff Info -->
       @php
           $staff = $farm->farm_operation_details ?? [];
           $permanent = $staff['staff_permanent'] ?? 0;
           $casual = $staff['staff_casual'] ?? 0;
           $totalStaff = $permanent + $casual;
       @endphp
       @if($totalStaff > 0)
       <div class="bg-white rounded-lg shadow-md p-6">
           <h2 class="text-xl font-bold text-gray-800 mb-4">👥 Staff / Labor</h2>
           <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
               <div class="text-center p-4 bg-blue-50 rounded-lg">
                   <p class="text-3xl font-bold text-blue-600">{{ $totalStaff }}</p>
                   <p class="text-sm text-gray-600">Total Workers</p>
               </div>
               <div class="text-center p-4 bg-green-50 rounded-lg">
                   <p class="text-3xl font-bold text-green-600">{{ $permanent }}</p>
                   <p class="text-sm text-gray-600">Permanent</p>
               </div>
               <div class="text-center p-4 bg-orange-50 rounded-lg">
                   <p class="text-3xl font-bold text-orange-600">{{ $casual }}</p>
                   <p class="text-sm text-gray-600">Casual</p>
               </div>
           </div>
       </div>
       @endif

       <!-- Farm Images -->
       <div class="bg-white rounded-lg shadow-md p-6">
           <div class="flex items-center justify-between mb-4">
               <h2 class="text-xl font-bold text-gray-800">📷 Farm Images</h2>
               <!-- Upload Button triggers modal -->
               <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition" data-bs-toggle="modal" data-bs-target="#uploadImageModal">
                   <i class="fas fa-upload mr-2"></i>Upload Image
               </button>
           </div>

           @if($farm->images->count() > 0)
               <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                   @foreach($farm->images as $image)
                       <div class="relative group border rounded-lg overflow-hidden">
                           <img src="{{ asset('storage/' . $image->image_path) }}" 
                                alt="{{ $image->caption ?? 'Farm image' }}" 
                                class="w-full h-40 object-cover">
                           @if($image->caption)
                               <p class="text-xs text-gray-600 p-2 truncate">{{ $image->caption }}</p>
                           @endif
                           <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                               @if($farm->main_image_id !== $image->id)
                                   <form action="{{ route('farms.images.set-main', [$farm, $image]) }}" method="POST">
                                       @csrf
                                       @method('POST')
                                       <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-full" title="Set as main">
                                           <i class="fas fa-star"></i>
                                       </button>
                                   </form>
                               @else
                                   <span class="bg-yellow-500 text-white p-2 rounded-full" title="Main image">
                                       <i class="fas fa-star"></i>
                                   </span>
                               @endif
                               <form action="{{ route('farms.images.destroy', [$farm, $image]) }}" method="POST" onsubmit="return confirm('Delete this image?')" class="inline">
                                   @csrf
                                   @method('DELETE')
                                   <button type="submit" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full" title="Delete">
                                       <i class="fas fa-trash"></i>
                                   </button>
                               </form>
                           </div>
                       </div>
                   @endforeach
               </div>
           @else
               <p class="text-gray-500 text-center py-4">No farm images uploaded yet.</p>
           @endif

           <!-- Upload Modal -->
           <div class="modal fade" id="uploadImageModal" tabindex="-1" aria-hidden="true">
               <div class="modal-dialog modal-dialog-centered">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title">Upload Farm Image</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <form action="{{ route('farms.images.store', $farm) }}" method="POST" enctype="multipart/form-data">
                           @csrf
                           <div class="modal-body">
                               <div class="mb-3">
                                   <label for="image" class="form-label">Select Image *</label>
                                   <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                                   @error('image')
                                       <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                   @enderror
                               </div>
                               <div class="mb-3">
                                   <label for="caption" class="form-label">Caption (optional)</label>
                                   <input type="text" name="caption" id="caption" class="form-control" value="{{ old('caption') }}" placeholder="e.g., Main farm entrance">
                               </div>
                           </div>
                           <div class="modal-footer">
                               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                               <button type="submit" class="btn btn-primary">Upload</button>
                           </div>
                       </form>
                   </div>
               </div>
           </div>
       </div>

       <!-- Farm Documents -->
       <div class="bg-white rounded-lg shadow-md p-6">
           <div class="flex items-center justify-between mb-4">
               <h2 class="text-xl font-bold text-gray-800">📄 Farm Documents</h2>
               <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                   <i class="fas fa-upload mr-2"></i>Upload Document
               </button>
           </div>

           @if($farm->documents->count() > 0)
               <div class="overflow-x-auto">
                   <table class="min-w-full divide-y divide-gray-200">
                       <thead class="bg-gray-50">
                           <tr>
                               <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Document Type</th>
                               <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">File Name</th>
                               <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Uploaded By</th>
                               <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                               <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                           </tr>
                       </thead>
                       <tbody class="bg-white divide-y divide-gray-200">
                           @foreach($farm->documents as $doc)
                               <tr>
                                   <td class="px-6 py-4 whitespace-nowrap">
                                       <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                           {{ $doc->document_type === 'ownership' ? 'bg-blue-100 text-blue-800' : 
                                              ($doc->document_type === 'title_deed' ? 'bg-purple-100 text-purple-800' : 
                                              ($doc->document_type === 'tax_compliance' ? 'bg-green-100 text-green-800' : 
                                              ($doc->document_type === 'insurance' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'))) }}">
                                           {{ ucfirst(str_replace('_', ' ', $doc->document_type)) }}
                                       </span>
                                   </td>
                                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                       {{ $doc->original_name }}
                                   </td>
                                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                       {{ $doc->uploader?->name ?? 'Unknown' }}
                                   </td>
                                   <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                       {{ $doc->created_at->format('M d, Y') }}
                                   </td>
                                   <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                       <div class="flex items-center gap-2">
                                           <a href="{{ route('farms.documents.show', [$farm, $doc]) }}" class="text-blue-600 hover:text-blue-800" target="_blank" title="Download/View">
                                               <i class="fas fa-download"></i>
                                           </a>
                                           <form action="{{ route('farms.documents.destroy', [$farm, $doc]) }}" method="POST" onsubmit="return confirm('Delete this document?')" class="inline">
                                               @csrf
                                               @method('DELETE')
                                               <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                   <i class="fas fa-trash"></i>
                                               </button>
                                           </form>
                                       </div>
                                   </td>
                               </tr>
                           @endforeach
                       </tbody>
                   </table>
               </div>
           @else
               <p class="text-gray-500 text-center py-4">No documents uploaded yet.</p>
           @endif

           <!-- Upload Document Modal -->
           <div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-hidden="true">
               <div class="modal-dialog modal-dialog-centered">
                   <div class="modal-content">
                       <div class="modal-header">
                           <h5 class="modal-title">Upload Farm Document</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                       </div>
                       <form action="{{ route('farms.documents.store', $farm) }}" method="POST" enctype="multipart/form-data">
                           @csrf
                           <div class="modal-body">
                               <div class="mb-3">
                                   <label for="document" class="form-label">Select File *</label>
                                   <input type="file" name="document" id="document" class="form-control" required>
                                   @error('document')
                                       <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                   @enderror
                                   <small class="text-muted">Max 10MB. Supported: PDF, DOC, DOCX, JPG, PNG, etc.</small>
                               </div>
                               <div class="mb-3">
                                   <label for="document_type" class="form-label">Document Type *</label>
                                   <select name="document_type" id="document_type" class="form-select" required>
                                       <option value="">Select type...</option>
                                       <option value="ownership">Ownership Certificate</option>
                                       <option value="title_deed">Title Deed</option>
                                       <option value="tax_compliance">Tax Compliance</option>
                                       <option value="insurance">Insurance Policy</option>
                                       <option value="certification">Certification</option>
                                       <option value="other">Other</option>
                                   </select>
                               </div>
                               <div class="mb-3">
                                   <label for="original_name" class="form-label">Custom Name (optional)</label>
                                   <input type="text" name="original_name" class="form-control" placeholder="e.g., Title Deed 2024">
                                   <small class="text-muted">Leave blank to use original filename</small>
                               </div>
                           </div>
                           <div class="modal-footer">
                               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                               <button type="submit" class="btn btn-primary">Upload</button>
                           </div>
                       </form>
                   </div>
               </div>
           </div>
       </div>

       <!-- Fields -->
     <div class="bg-white rounded-lg shadow-md p-6">
         <h2 class="text-xl font-bold text-gray-800 mb-4">Fields</h2>
         @if($farm->fields->count() > 0)
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                 @foreach($farm->fields as $field)
                     <div class="border rounded-lg p-4 hover:shadow-lg transition">
                         <div class="flex items-center justify-between mb-2">
                             <h3 class="font-semibold text-gray-800">{{ $field->name }}</h3>
                             <span class="text-sm text-gray-500">{{ $field->size_hectares ?? 'N/A' }} ha</span>
                         </div>
                         <p class="text-sm text-gray-500 mb-2">{{ $field->location ?? 'No location' }}</p>
                         <div class="flex items-center justify-between">
                             <span class="text-sm text-primary">{{ $field->sensors->count() }} sensors</span>
                             <a href="{{ route('fields.show', $field->id) }}" class="text-sm text-primary hover:text-primary-dark">
                                 View Details <i class="fas fa-arrow-right ml-1"></i>
                             </a>
                         </div>
                     </div>
                 @endforeach
             </div>
         @else
             <p class="text-gray-500 text-center py-4">No fields available for this farm.</p>
         @endif
         <div class="mt-4">
             <a href="{{ route('fields.create') }}?farm_id={{ $farm->id }}" class="text-primary hover:text-primary-dark font-medium">
                 <i class="fas fa-plus mr-2"></i>Add Field
             </a>
         </div>
     </div>
 </div>
 @endsection
