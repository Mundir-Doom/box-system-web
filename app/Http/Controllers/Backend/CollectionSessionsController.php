<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backend\CollectionSession;
use App\Models\Backend\CollectionPeriod;
use App\Models\Backend\Parcel;
use App\Repositories\CollectionSession\CollectionSessionInterface;
use App\Repositories\CollectionPeriod\CollectionPeriodInterface;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class CollectionSessionsController extends Controller
{
    protected $sessionRepo;
    protected $periodRepo;

    public function __construct(
        CollectionSessionInterface $sessionRepo,
        CollectionPeriodInterface $periodRepo
    ) {
        $this->sessionRepo = $sessionRepo;
        $this->periodRepo = $periodRepo;
    }

    /**
     * Display a listing of collection sessions
     */
    public function index(Request $request)
    {
        $sessions = $this->sessionRepo->getSessionsWithCounts();
        
        // Apply filters if provided
        if ($request->has('date_from') && $request->has('date_to')) {
            $sessions = $this->sessionRepo->getSessionsByDateRange(
                $request->date_from,
                $request->date_to
            );
        }

        $periods = $this->periodRepo->getActive();
        
        return view('backend.collection_sessions.index', compact('sessions', 'periods'));
    }

    /**
     * Show current active collection sessions
     */
    public function current()
    {
        $currentSessions = $this->sessionRepo->getCurrent();
        $activePeriods = $this->periodRepo->getCurrentActivePeriods();
        
        return view('backend.collection_sessions.current', compact('currentSessions', 'activePeriods'));
    }

    /**
     * Show historical collection sessions
     */
    public function history(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        
        $sessions = $this->sessionRepo->getSessionsByDateRange($startDate, $endDate);
        
        return view('backend.collection_sessions.history', compact('sessions', 'startDate', 'endDate'));
    }

    /**
     * Show specific collection session details
     */
    public function show($id)
    {
        try {
            $session = $this->sessionRepo->findById($id);
            $unassignedParcels = $session->getUnassignedParcels();
            
            return view('backend.collection_sessions.show', compact('session', 'unassignedParcels'));
        } catch (\Exception $e) {
            Toastr::error('Collection session not found!', 'Error');
            return redirect()->route('collection-sessions.index');
        }
    }

    /**
     * Start a new collection session
     */
    public function start(Request $request)
    {
        $request->validate([
            'collection_period_id' => 'required|exists:collection_periods,id',
            'collection_date' => 'nullable|date'
        ]);

        try {
            $date = $request->collection_date ?: Carbon::today();
            $session = $this->sessionRepo->startSession($request->collection_period_id, $date);

            Toastr::success('Collection session started successfully!', 'Success');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $session,
                    'message' => 'Collection session started successfully!'
                ]);
            }

            return redirect()->route('collection-sessions.show', $session->id);
        } catch (\Exception $e) {
            Toastr::error('Failed to start collection session: ' . $e->getMessage(), 'Error');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back();
        }
    }

    /**
     * Complete a collection session
     */
    public function complete($id)
    {
        try {
            $session = $this->sessionRepo->completeSession($id);

            Toastr::success('Collection session completed successfully!', 'Success');
            return redirect()->route('collection-sessions.index');
        } catch (\Exception $e) {
            Toastr::error('Failed to complete collection session: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }

    /**
     * Add parcel to collection session
     */
    public function addParcel(Request $request, $id)
    {
        $request->validate([
            'parcel_id' => 'required|exists:parcels,id',
            'notes' => 'nullable|string|max:500'
        ]);

        try {
            $session = $this->sessionRepo->findById($id);
            $parcel = Parcel::findOrFail($request->parcel_id);

            // Check if parcel is already in a collection session
            if ($parcel->collection_session_id) {
                throw new \Exception('Parcel is already assigned to a collection session.');
            }

            $assignment = $session->addParcel($parcel, $request->notes);

            Toastr::success('Parcel added to collection session successfully!', 'Success');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $assignment,
                    'message' => 'Parcel added successfully!'
                ]);
            }

            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Failed to add parcel: ' . $e->getMessage(), 'Error');
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->back();
        }
    }

    /**
     * Remove parcel from collection session
     */
    public function removeParcel($id, $parcelId)
    {
        try {
            $session = $this->sessionRepo->findById($id);
            $assignment = $session->parcelAssignments()->where('parcel_id', $parcelId)->first();

            if (!$assignment) {
                throw new \Exception('Parcel not found in this collection session.');
            }

            // Update parcel
            $assignment->parcel()->update([
                'collection_session_id' => null,
                'collected_at' => null
            ]);

            // Delete assignment
            $assignment->delete();

            // Update session counts
            $session->updateCounts();

            Toastr::success('Parcel removed from collection session successfully!', 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Failed to remove parcel: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }

    /**
     * Get sessions data for AJAX calls
     */
    public function getSessionsData(Request $request)
    {
        try {
            if ($request->has('current')) {
                $sessions = $this->sessionRepo->getCurrent();
            } else if ($request->has('active')) {
                $sessions = $this->sessionRepo->getActive();
            } else {
                $sessions = $this->sessionRepo->getAll();
            }

            return response()->json([
                'success' => true,
                'data' => $sessions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk add parcels to collection session
     */
    public function bulkAddParcels(Request $request, $id)
    {
        $request->validate([
            'parcel_ids' => 'required|array',
            'parcel_ids.*' => 'exists:parcels,id'
        ]);

        try {
            $session = $this->sessionRepo->findById($id);
            $addedCount = 0;
            $skippedCount = 0;

            foreach ($request->parcel_ids as $parcelId) {
                $parcel = Parcel::find($parcelId);
                
                if ($parcel && !$parcel->collection_session_id) {
                    $session->addParcel($parcel);
                    $addedCount++;
                } else {
                    $skippedCount++;
                }
            }

            $message = "Added {$addedCount} parcels to collection session.";
            if ($skippedCount > 0) {
                $message .= " Skipped {$skippedCount} parcels (already assigned).";
            }

            Toastr::success($message, 'Success');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Failed to add parcels: ' . $e->getMessage(), 'Error');
            return redirect()->back();
        }
    }
}