<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of payment methods or render management UI
     */
    public function index(Request $request)
    {
        $methods = PaymentMethod::orderBy('id', 'asc')->get();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $methods
            ]);
        }

        return view('settings.payment_methods', compact('methods'));
    }

    /**
     * Store a newly created payment method with dynamic fields
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'method'            => 'required|string|max:50',
            'description'       => 'nullable|string|max:1000',
            'is_active'         => 'nullable|boolean',
            'fields'            => 'nullable|array',
            'fields.*.name'     => 'nullable|string|max:50',
            'fields.*.label'    => 'nullable|string|max:100',
            'fields.*.type'     => 'nullable|in:text,number,date',
            'fields.*.required' => 'nullable|boolean',
        ]);

        $formattedFields = [];
        if (!empty($validated['fields'])) {
            foreach ($validated['fields'] as $field) {
                $label = trim($field['label'] ?? '');
                $name  = trim($field['name'] ?? '');

                if ($label !== '') {
                    $slugName = !empty($name) ? Str::slug($name, '_') : Str::slug($label, '_');
                    $formattedFields[] = [
                        'name'     => $slugName,
                        'label'    => $label,
                        'type'     => $field['type'] ?? 'text',
                        'required' => !empty($field['required']),
                    ];
                }
            }
        }

        $paymentMethod = PaymentMethod::create([
            'method'      => trim($validated['method']),
            'description' => $validated['description'] ?? null,
            'fields'      => $formattedFields,
            'is_active'   => $request->boolean('is_active', true),
            'company_id'  => auth()->user()->company_id,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment method created successfully.',
                'data'    => $paymentMethod
            ], 201);
        }

        return redirect()->back()->with('success', 'Payment method created successfully.');
    }

    /**
     * Update an existing payment method
     */
    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);

        $validated = $request->validate([
            'method'            => 'required|string|max:50',
            'description'       => 'nullable|string|max:1000',
            'is_active'         => 'nullable|boolean',
            'fields'            => 'nullable|array',
            'fields.*.name'     => 'nullable|string|max:50',
            'fields.*.label'    => 'nullable|string|max:100',
            'fields.*.type'     => 'nullable|in:text,number,date',
            'fields.*.required' => 'nullable|boolean',
        ]);

        $formattedFields = [];
        if (!empty($validated['fields'])) {
            foreach ($validated['fields'] as $field) {
                $label = trim($field['label'] ?? '');
                $name  = trim($field['name'] ?? '');

                if ($label !== '') {
                    $slugName = !empty($name) ? Str::slug($name, '_') : Str::slug($label, '_');
                    $formattedFields[] = [
                        'name'     => $slugName,
                        'label'    => $label,
                        'type'     => $field['type'] ?? 'text',
                        'required' => !empty($field['required']),
                    ];
                }
            }
        }

        $paymentMethod->update([
            'method'      => trim($validated['method']),
            'description' => $validated['description'] ?? null,
            'fields'      => $formattedFields,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment method updated successfully.',
                'data'    => $paymentMethod
            ]);
        }

        return redirect()->back()->with('success', 'Payment method updated successfully.');
    }

    /**
     * Delete a payment method
     */
    public function destroy($id, Request $request)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment method deleted successfully.'
            ]);
        }

        return redirect()->back()->with('success', 'Payment method deleted successfully.');
    }
}
