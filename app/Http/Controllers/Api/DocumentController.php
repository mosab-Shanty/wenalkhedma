<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;
use Illuminate\Support\Facades\Gate;

class DocumentController extends Controller
{

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,service_id',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_type' => 'required|in:image,document,license,other',
            'description' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'الملف مرفوض أو خطأ في البيانات',
                'errors' => $validator->errors()
            ], 422);
        }

        $service = Service::findOrFail($request->service_id);

        Gate::authorize('uploadDocument', $service);

        if ($request->hasFile('file')) {

            $path = $request->file('file')->store('documents', 'public');
            $fileUrl = asset('storage/' . $path);

            $document = Document::create([
                'user_id' => $request->user()->user_id,
                'service_id' => $request->service_id,
                'file_type' => $request->file_type,
                'file_url' => $fileUrl,
                'description' => $request->description,
                'status' => 'pending',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'تم رفع الملف بنجاح وهو قيد المراجعة',
                'data' => $document
            ], 201);
        }

        return response()->json([
            'status' => false,
            'message' => 'لم يتم إرسال أي ملف'
        ], 400);
    }
}
