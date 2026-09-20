<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiQuery;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AiAssistantController extends Controller
{

    public function ask(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query_text' => 'required|string|min:3',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'يرجى كتابة السؤال بشكل أسبوع مفصل',
                'errors' => $validator->errors()
            ], 422);
        }

        $queryText = $request->query_text;

        // محاكاة بسيطة للذكاء الاصطناعي لتحديد النية (Intent Detection) والبحث عن الخدمة
        $service = Service::with(['location', 'category'])
            ->where('name', 'like', '%' . $queryText . '%')
            ->orWhere('description', 'like', '%' . $queryText . '%')
            ->first();

        // حفظ استفسار الـ AI في قاعدة البيانات
        $aiQuery = AiQuery::create([
            'user_id' => $request->user() ? $request->user()->user_id : null,
            'query_text' => $queryText,
            'detected_intent' => 'service_search',
            'result_service_id' => $service ? $service->service_id : null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تحليل السؤال بنجاح',
            'ai_response' => $service
                ? "بناءً على طلبك، نقترح عليك زيارة '{$service->name}' وهي حالياً بحالة: {$service->current_status}."
                : "عذراً، لم أستطع العثور على خدمة مطابقة لطلبك حالياً.",
            'suggested_service' => $service
        ]);
    }
}
