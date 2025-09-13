<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backend\CollectionPeriod;
use App\Repositories\CollectionPeriod\CollectionPeriodInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;

class CollectionPeriodsController extends Controller
{
    protected $repo;

    public function __construct(CollectionPeriodInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Display a listing of collection periods
     */
    public function index()
    {
        $periods = $this->repo->getAll();
        return view('backend.collection_periods.index', compact('periods'));
    }

    /**
     * Show the form for creating a new collection period
     */
    public function create()
    {
        return view('backend.collection_periods.create');
    }

    /**
     * Store a newly created collection period
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:collection_periods,name',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->only(['name', 'start_time', 'end_time', 'description']);
            $data['start_time'] = $data['start_time'] . ':00';
            $data['end_time'] = $data['end_time'] . ':00';
            $data['is_active'] = $request->has('is_active') ? true : false;

            $period = $this->repo->create($data);

            Toastr::success('Collection period created successfully!', 'Success');
            return redirect()->route('collection-periods.index');
        } catch (\Exception $e) {
            Toastr::error('Failed to create collection period: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified collection period
     */
    public function edit($id)
    {
        try {
            $period = $this->repo->findById($id);
            return view('backend.collection_periods.edit', compact('period'));
        } catch (\Exception $e) {
            Toastr::error('Collection period not found!', 'Error');
            return redirect()->route('collection-periods.index');
        }
    }

    /**
     * Update the specified collection period
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:collection_periods,name,' . $id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->only(['name', 'start_time', 'end_time', 'description']);
            $data['start_time'] = $data['start_time'] . ':00';
            $data['end_time'] = $data['end_time'] . ':00';
            $data['is_active'] = $request->has('is_active') ? true : false;

            $period = $this->repo->update($id, $data);

            Toastr::success('Collection period updated successfully!', 'Success');
            return redirect()->route('collection-periods.index');
        } catch (\Exception $e) {
            Toastr::error('Failed to update collection period: ' . $e->getMessage(), 'Error');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified collection period
     */
    public function destroy($id)
    {
        try {
            $this->repo->delete($id);
            Toastr::success('Collection period deleted successfully!', 'Success');
            return redirect()->route('collection-periods.index');
        } catch (\Exception $e) {
            Toastr::error('Failed to delete collection period: ' . $e->getMessage(), 'Error');
            return redirect()->route('collection-periods.index');
        }
    }

    /**
     * Toggle the active status of a collection period
     */
    public function toggle($id)
    {
        try {
            $period = $this->repo->toggle($id);
            $status = $period->is_active ? 'activated' : 'deactivated';
            
            Toastr::success("Collection period {$status} successfully!", 'Success');
            return redirect()->route('collection-periods.index');
        } catch (\Exception $e) {
            Toastr::error('Failed to toggle collection period status: ' . $e->getMessage(), 'Error');
            return redirect()->route('collection-periods.index');
        }
    }

    /**
     * Get currently active periods (for AJAX calls)
     */
    public function getCurrentActive()
    {
        try {
            $periods = $this->repo->getCurrentActivePeriods();
            return response()->json([
                'success' => true,
                'data' => $periods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}