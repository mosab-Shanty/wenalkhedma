<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = ServiceCategory::withCount('services')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:service_categories,name'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'اسم التصنيف مطلوب',
            'name.unique' => 'هذا التصنيف موجود بالفعل',
        ]);

        ServiceCategory::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

        return back()->with('success', 'تم إضافة التصنيف بنجاح.');
    }

    public function update(Request $request, $id)
    {
        $category = ServiceCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:service_categories,name,' . $id . ',category_id'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $category->update($validated);

        return back()->with('success', 'تم تحديث التصنيف بنجاح.');
    }

    public function destroy($id)
    {
        $category = ServiceCategory::findOrFail($id);
        $category->delete();

        return back()->with('success', 'تم حذف التصنيف بنجاح.');
    }
}
