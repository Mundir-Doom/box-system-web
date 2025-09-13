<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\Hub;
use App\Models\Backend\HubTransferCharge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HubPricingController extends Controller
{
    public function index()
    {
        $hubs = Hub::all();
        return view('backend.hub-pricing.index', compact('hubs'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_hub_id' => 'required|exists:hubs,id',
            'to_hub_id' => 'required|exists:hubs,id|different:from_hub_id',
            'base_charge' => 'required|numeric|min:0',
            'per_kg_rate' => 'nullable|numeric|min:0',
            'min_charge' => 'nullable|numeric|min:0',
            'max_charge' => 'nullable|numeric|min:0|gte:min_charge',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        try {
            $pricing = HubTransferCharge::updateOrCreate(
                [
                    'from_hub_id' => $request->from_hub_id,
                    'to_hub_id' => $request->to_hub_id,
                ],
                [
                    'base_charge' => $request->base_charge,
                    'per_km_rate' => $request->per_kg_rate ?? 0, // Using per_km_rate field for per_kg_rate
                    'min_charge' => $request->min_charge ?? 0,
                    'max_charge' => $request->max_charge ?? 999999,
                    'weight_factor' => 1.0, // Default weight factor
                    'status' => 1,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Hub pricing saved successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving pricing: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $pricing = HubTransferCharge::findOrFail($id);
            $pricing->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pricing deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting pricing: ' . $e->getMessage()
            ], 500);
        }
    }
}
