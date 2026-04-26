<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentUploadRequest;
use App\Http\Requests\FarmerRegisterRequest;
use App\Http\Requests\FarmerUpdateRequest;
use App\Models\Farmer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FarmerController extends Controller
{
    public function register(FarmerRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = \App\Models\User::create([
            'name' => "{$data['first_name']} {$data['last_name']}",
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'farmer',
        ]);

        $farmer = Farmer::create([
            'user_id' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender' => $data['gender'] ?? null,
            'address' => $data['address'] ?? null,
            'village' => $data['village'] ?? null,
            'ward' => $data['ward'] ?? null,
            'district' => $data['district'] ?? null,
            'region' => $data['region'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'farm_size_hectares' => $data['farm_size_hectares'] ?? null,
            'farm_type' => $data['farm_type'] ?? 'smallholder',
            'crop_history' => $data['crop_history'] ?? null,
            'farming_methods' => $data['farming_methods'] ?? null,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        // Also create a session for hybrid web access
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Farmer registered successfully',
            'user' => $user,
            'farmer' => $farmer,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if ($user->role !== 'farmer') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        // Also create a session for hybrid web access
        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'farmer' => $user->farmer,
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function profile(Request $request): JsonResponse
    {
        $farmer = $request->user()->farmer;

        if (! $farmer) {
            return response()->json(['message' => 'Farmer profile not found'], 404);
        }

        $farmer->load(['documents', 'cropHistories']);

        return response()->json([
            'farmer' => $farmer,
        ]);
    }

    public function updateProfile(FarmerUpdateRequest $request): JsonResponse
    {
        $farmer = $request->user()->farmer;

        if (! $farmer) {
            return response()->json(['message' => 'Farmer profile not found'], 404);
        }

        $data = $request->validated();
        $farmer->update($data);

        if (isset($data['user']['email'])) {
            $request->user()->update(['email' => $data['user']['email']]);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'farmer' => $farmer->fresh()->load(['documents', 'cropHistories']),
        ]);
    }

    public function updateFarmDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'farm_size_hectares' => 'nullable|numeric|min:0',
            'farm_type' => 'nullable|in:smallholder,medium,large',
            'crop_history' => 'nullable|array',
            'farming_methods' => 'nullable|array',
            'latitude' => 'nullable|between:-90,90',
            'longitude' => 'nullable|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $farmer = $request->user()->farmer;

        if (! $farmer) {
            return response()->json(['message' => 'Farmer profile not found'], 404);
        }

        $farmer->update($validator->validated());

        return response()->json([
            'message' => 'Farm details updated successfully',
            'farmer' => $farmer->fresh(),
        ]);
    }

    public function getCropHistory(Request $request): JsonResponse
    {
        $farmer = $request->user()->farmer;

        if (! $farmer) {
            return response()->json(['message' => 'Farmer profile not found'], 404);
        }

        $cropHistories = $farmer->cropHistories()->orderBy('year', 'desc')->get();

        return response()->json([
            'crop_histories' => $cropHistories,
        ]);
    }

    public function addCropHistory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'crop_name' => 'required|string|max:255',
            'crop_id' => 'nullable|exists:crops,id',
            'year' => 'required|integer|min:1900|max:'.date('Y'),
            'hectares' => 'nullable|numeric|min:0',
            'expected_yield' => 'nullable|numeric|min:0',
            'actual_yield' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:50',
            'income' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $farmer = $request->user()->farmer;

        if (! $farmer) {
            return response()->json(['message' => 'Farmer profile not found'], 404);
        }

        $cropHistory = $farmer->cropHistories()->create($validator->validated());

        return response()->json([
            'message' => 'Crop history added successfully',
            'crop_history' => $cropHistory,
        ], 201);
    }

    public function updateCropHistory(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'crop_name' => 'sometimes|string|max:255',
            'crop_id' => 'nullable|exists:crops,id',
            'year' => 'sometimes|integer|min:1900|max:'.date('Y'),
            'hectares' => 'nullable|numeric|min:0',
            'expected_yield' => 'nullable|numeric|min:0',
            'actual_yield' => 'nullable|numeric|min:0',
            'yield_unit' => 'nullable|string|max:50',
            'income' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $farmer = $request->user()->farmer;
        $cropHistory = $farmer->cropHistories()->find($id);

        if (! $cropHistory) {
            return response()->json(['message' => 'Crop history not found'], 404);
        }

        $cropHistory->update($validator->validated());

        return response()->json([
            'message' => 'Crop history updated successfully',
            'crop_history' => $cropHistory->fresh(),
        ]);
    }

    public function deleteCropHistory(Request $request, int $id): JsonResponse
    {
        $farmer = $request->user()->farmer;
        $cropHistory = $farmer->cropHistories()->find($id);

        if (! $cropHistory) {
            return response()->json(['message' => 'Crop history not found'], 404);
        }

        $cropHistory->delete();

        return response()->json(['message' => 'Crop history deleted successfully']);
    }

    public function uploadDocument(DocumentUploadRequest $request): JsonResponse
    {
        $farmer = $request->user()->farmer;

        if (! $farmer) {
            return response()->json(['message' => 'Farmer profile not found'], 404);
        }

        $file = $request->file('document');

        $path = $file->store('farmer-documents/'.$farmer->id, 'public');

        $document = $farmer->documents()->create([
            'title' => $request->title,
            'document_type' => $request->document_type,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => $document,
        ], 201);
    }

    public function getDocuments(Request $request): JsonResponse
    {
        $farmer = $request->user()->farmer;

        if (! $farmer) {
            return response()->json(['message' => 'Farmer profile not found'], 404);
        }

        $documents = $farmer->documents()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'documents' => $documents,
        ]);
    }

    public function deleteDocument(Request $request, int $id): JsonResponse
    {
        $farmer = $request->user()->farmer;
        $document = $farmer->documents()->find($id);

        if (! $document) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return response()->json(['message' => 'Document deleted successfully']);
    }

    public function index(Request $request): JsonResponse
    {
        $query = Farmer::with(['documents', 'cropHistories']);

        if ($request->has('region')) {
            $query->where('region', $request->region);
        }

        if ($request->has('district')) {
            $query->where('district', $request->district);
        }

        if ($request->has('farm_type')) {
            $query->where('farm_type', $request->farm_type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $farmers = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($farmers);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $farmer = Farmer::with(['documents', 'cropHistories'])->find($id);

        if (! $farmer) {
            return response()->json(['message' => 'Farmer not found'], 404);
        }

        return response()->json(['farmer' => $farmer]);
    }
}
