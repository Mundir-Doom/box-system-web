<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Backend\CollectionSession;
use App\Models\Backend\ParcelCollectionAssignment;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Parcel;
use App\Repositories\CollectionSession\CollectionSessionInterface;
use App\Repositories\DeliveryMan\DeliveryManInterface;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;

class DeliveryAssignmentController extends Controller
{
    protected $sessionRepo;
    protected $deliveryManRepo;

    public function __construct(
        CollectionSessionInterface $sessionRepo,
        DeliveryManInterface $deliveryManRepo
    ) {
        $this->sessionRepo = $sessionRepo;
        $this->deliveryManRepo = $deliveryManRepo;
    }

    /**
     * Show delivery assignment dashboard
     */
    public function index(Request $request)
    {
        // Get current active collection sessions
        $activeSessions = $this->sessionRepo->getActive();
        $selectedSessionId = $request->get('session');

        // Get unassigned parcels from active sessions (optionally filter by session)
        $unassignedParcelsQuery = ParcelCollectionAssignment::unassigned()
            ->whereHas('collectionSession', function($query) {
                $query->where('status', 'active');
            })
            ->with(['parcel', 'collectionSession.collectionPeriod']);

        if (!empty($selectedSessionId)) {
            $unassignedParcelsQuery->where('collection_session_id', $selectedSessionId);
        }

        $unassignedParcels = $unassignedParcelsQuery->get();

        // Get available delivery men
        $availableDeliveryMen = $this->deliveryManRepo->all();

        // Get delivery assignments summary
        $assignmentStats = $this->getAssignmentStats();

        return view('backend.delivery_assignments.index', compact(
            'activeSessions',
            'unassignedParcels', 
            'availableDeliveryMen',
            'assignmentStats'
        ));
    }

    /**
     * Assign parcels to delivery person (supports both single and bulk assignment)
     */
    public function assign(Request $request)
    {
        $request->validate([
            'assignment_ids' => 'required|array',
            'assignment_ids.*' => 'exists:parcel_collection_assignments,id',
            'delivery_man_id' => 'required|exists:delivery_man,id',
            'priority' => 'nullable|integer|in:-1,0,1'
        ]);

        try {
            $assignmentIds = $request->assignment_ids;
            $deliveryManId = $request->delivery_man_id;
            $priority = $request->priority ?? 0;
            
            $assignedCount = 0;
            
            foreach ($assignmentIds as $assignmentId) {
                $assignment = ParcelCollectionAssignment::findOrFail($assignmentId);
                $assignment->assignTo($deliveryManId, $priority);
                $assignment->collectionSession->updateCounts();
                $assignedCount++;
            }

            if ($assignedCount === 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Parcel assigned successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => "{$assignedCount} parcels assigned successfully!"
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign parcels: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get assignment statistics
     */
    public function getAssignmentStats()
    {
        $today = Carbon::today();
        
        return [
            'total_collected_today' => ParcelCollectionAssignment::whereDate('collected_at', $today)->count(),
            'total_assigned_today' => ParcelCollectionAssignment::whereDate('assigned_at', $today)->count(),
            'unassigned_parcels' => ParcelCollectionAssignment::unassigned()->count(),
            'active_sessions' => CollectionSession::active()->count()
        ];
    }

    /**
     * Get available delivery men with real workload data
     */
    public function getAvailableDeliveryMen(Request $request)
    {
        try {
            // Get all active delivery men with their current workload
            $deliveryMen = DeliveryMan::with('user')
                ->where('status', 1)
                ->get()
                ->map(function($deliveryMan) {
                    // Calculate real active assignments (assigned but not delivered)
                    $activeAssignments = ParcelCollectionAssignment::where('delivery_man_id', $deliveryMan->id)
                        ->whereIn('assignment_status', ['assigned', 'out_for_delivery'])
                        ->count();
                    
                    // Calculate today's assignments
                    $todayAssignments = ParcelCollectionAssignment::where('delivery_man_id', $deliveryMan->id)
                        ->whereDate('assigned_at', today())
                        ->count();
                    
                    // Calculate delivered today
                    $deliveredToday = ParcelCollectionAssignment::where('delivery_man_id', $deliveryMan->id)
                        ->where('assignment_status', 'delivered')
                        ->whereDate('updated_at', today())
                        ->count();
                    
                    return [
                        'id' => $deliveryMan->id,
                        'name' => $deliveryMan->user ? $deliveryMan->user->name : 'Unknown',
                        'email' => $deliveryMan->user ? $deliveryMan->user->email : '',
                        'phone' => $deliveryMan->user ? $deliveryMan->user->mobile : '',
                        'active_assignments' => $activeAssignments,
                        'today_assignments' => $todayAssignments,
                        'delivered_today' => $deliveredToday,
                        'capacity_status' => $this->getCapacityStatus($activeAssignments)
                    ];
                })
                ->sortBy('active_assignments'); // Sort by workload (least busy first)
            
            return response()->json(['success' => true, 'data' => $deliveryMen->values()]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get capacity status based on workload (realistic thresholds for courier service)
     */
    private function getCapacityStatus($activeAssignments)
    {
        if ($activeAssignments >= 20) return 'full';      // 20+ parcels = overloaded
        if ($activeAssignments >= 15) return 'high';      // 15-19 parcels = high workload
        if ($activeAssignments >= 8) return 'medium';     // 8-14 parcels = medium workload
        if ($activeAssignments >= 1) return 'low';        // 1-7 parcels = light workload
        return 'available';                               // 0 parcels = available
    }
}
