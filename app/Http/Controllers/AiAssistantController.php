<?php

namespace App\Http\Controllers;

use App\Models\AiQuery;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AiAssistantController extends Controller
{
    public function ask(Request $request)
    {
        $request->validate([
            'prompt' => ['required', 'string', 'max:500'],
        ]);

        $prompt = trim($request->input('prompt'));
        $lowerPrompt = mb_strtolower($prompt);

        $query = Service::with(['category', 'location', 'documents'])
            ->where('approval_status', 'approved');

        $detectedCategory = null;
        $statusFilter = null;
        $locationFilter = null;

        // 1. Detect Category
        $categories = ServiceCategory::all();
        foreach ($categories as $cat) {
            if (mb_strpos($lowerPrompt, mb_strtolower($cat->name)) !== false) {
                $detectedCategory = $cat;
                break;
            }
        }

        // Synonyms matching if exact name not found
        if (!$detectedCategory) {
            if (mb_strpos($lowerPrompt, 'صيدل') !== false || mb_strpos($lowerPrompt, 'دواء') !== false || mb_strpos($lowerPrompt, 'علاج') !== false || mb_strpos($lowerPrompt, 'طبي') !== false) {
                $detectedCategory = ServiceCategory::where('name', 'like', '%صيدل%')->first();
            } elseif (mb_strpos($lowerPrompt, 'مخبز') !== false || mb_strpos($lowerPrompt, 'خبز') !== false || mb_strpos($lowerPrompt, 'طحين') !== false) {
                $detectedCategory = ServiceCategory::where('name', 'like', '%مخبز%')->first();
            } elseif (mb_strpos($lowerPrompt, 'ماء') !== false || mb_strpos($lowerPrompt, 'مياه') !== false || mb_strpos($lowerPrompt, 'شرب') !== false) {
                $detectedCategory = ServiceCategory::where('name', 'like', '%مياه%')->first();
            } elseif (mb_strpos($lowerPrompt, 'عياد') !== false || mb_strpos($lowerPrompt, 'مستشفى') !== false || mb_strpos($lowerPrompt, 'إسعاف') !== false) {
                $detectedCategory = ServiceCategory::where('name', 'like', '%عياد%')->first();
            } elseif (mb_strpos($lowerPrompt, 'كهرب') !== false || mb_strpos($lowerPrompt, 'شحن') !== false || mb_strpos($lowerPrompt, 'بطاري') !== false) {
                $detectedCategory = ServiceCategory::where('name', 'like', '%كهرب%')->first();
            }
        }

        // 2. Detect Status Intent
        if (mb_strpos($lowerPrompt, 'متاح') !== false || mb_strpos($lowerPrompt, 'مفتوح') !== false || mb_strpos($lowerPrompt, 'شغال') !== false || mb_strpos($lowerPrompt, 'الآن') !== false) {
            $statusFilter = 'open';
        }

        // 3. Detect Location Intent
        $cities = ['غزة', 'الرمال', 'خان يونس', 'رفح', 'الوسطى', 'دير البلح', 'شمال غزة', 'النصيرات'];
        foreach ($cities as $city) {
            if (mb_strpos($lowerPrompt, $city) !== false) {
                $locationFilter = $city;
                break;
            }
        }

        // Build Eloquent Query
        if ($detectedCategory) {
            $query->where('category_id', $detectedCategory->category_id);
        }

        if ($statusFilter) {
            $query->where('current_status', 'open');
        }

        if ($locationFilter) {
            $query->whereHas('location', function ($lq) use ($locationFilter) {
                $lq->where('city', 'like', "%{$locationFilter}%")
                   ->orWhere('area', 'like', "%{$locationFilter}%")
                   ->orWhere('governorate', 'like', "%{$locationFilter}%");
            });
        }

        // Broad text search if no category or location matched specifically
        if (!$detectedCategory && !$locationFilter) {
            $query->where(function ($q) use ($prompt) {
                $q->where('name', 'like', "%{$prompt}%")
                  ->orWhere('description', 'like', "%{$prompt}%");
            });
        }

        $results = $query->latest()->take(5)->get();

        // Safely log query without throwing database errors
        if (Auth::check()) {
            try {
                AiQuery::create([
                    'user_id' => Auth::user()->user_id,
                    'query_text' => $prompt,
                    'detected_intent' => $detectedCategory ? $detectedCategory->name : 'بحث عام',
                    'result_service_id' => $results->first() ? $results->first()->service_id : null,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to log AI query: ' . $e->getMessage());
            }
        }

        $responseHtml = view('components.ai-results', [
            'results' => $results,
            'detectedCategory' => $detectedCategory,
            'statusFilter' => $statusFilter,
            'locationFilter' => $locationFilter,
            'prompt' => $prompt,
        ])->render();

        return response()->json([
            'success' => true,
            'count' => $results->count(),
            'html' => $responseHtml,
        ]);
    }
}
