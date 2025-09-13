<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;
use App\Enums\CollectionSessionStatus;
use App\Enums\ParcelAssignmentStatus;

class CollectionSession extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'collection_period_id',
        'collection_date',
        'started_at',
        'completed_at',
        'status',
        'total_parcels',
        'assigned_parcels',
        'unassigned_parcels',
        'notes'
    ];

    protected $casts = [
        'collection_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_parcels' => 'integer',
        'assigned_parcels' => 'integer',
        'unassigned_parcels' => 'integer'
    ];

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('CollectionSession')
            ->logOnly(['collection_period_id', 'collection_date', 'status', 'total_parcels'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    /**
     * Scope to get active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get sessions for specific date
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('collection_date', $date);
    }

    /**
     * Get the collection period
     */
    public function collectionPeriod()
    {
        return $this->belongsTo(CollectionPeriod::class, 'collection_period_id');
    }

    /**
     * Get parcel collection assignments
     */
    public function parcelAssignments()
    {
        return $this->hasMany(ParcelCollectionAssignment::class);
    }

    /**
     * Get parcels through assignments
     */
    public function parcels()
    {
        return $this->hasManyThrough(
            Parcel::class,
            ParcelCollectionAssignment::class,
            'collection_session_id',
            'id',
            'id',
            'parcel_id'
        );
    }

    /**
     * Start the collection session
     */
    public function startCollection()
    {
        $this->update([
            'status' => 'active',
            'started_at' => Carbon::now()
        ]);

        return $this;
    }

    /**
     * Complete the collection session
     */
    public function completeCollection()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => Carbon::now()
        ]);

        return $this;
    }

    /**
     * Add parcel to collection session
     */
    public function addParcel(Parcel $parcel, $notes = null)
    {
        $assignment = ParcelCollectionAssignment::create([
            'parcel_id' => $parcel->id,
            'collection_session_id' => $this->id,
            'collected_at' => Carbon::now(),
            'assignment_status' => 'collected',
            'notes' => $notes
        ]);

        // Update parcel
        $parcel->update([
            'collection_session_id' => $this->id,
            'collected_at' => Carbon::now()
        ]);

        // Update session counts
        $this->updateCounts();

        return $assignment;
    }

    /**
     * Get unassigned parcels
     */
    public function getUnassignedParcels()
    {
        return $this->parcelAssignments()
                   ->where('assignment_status', 'collected')
                   ->whereNull('delivery_man_id')
                   ->with('parcel')
                   ->get();
    }

    /**
     * Assign parcels to delivery man
     */
    public function assignToDeliveryMan($parcelIds, $deliveryManId, $priority = 0)
    {
        $assignments = $this->parcelAssignments()
                          ->whereIn('parcel_id', $parcelIds)
                          ->get();

        foreach ($assignments as $assignment) {
            $assignment->update([
                'delivery_man_id' => $deliveryManId,
                'assigned_at' => Carbon::now(),
                'assignment_status' => 'assigned',
                'priority' => $priority
            ]);
        }

        $this->updateCounts();

        return $assignments;
    }

    /**
     * Update parcel counts
     */
    public function updateCounts()
    {
        $total = $this->parcelAssignments()->count();
        $assigned = $this->parcelAssignments()->whereNotNull('delivery_man_id')->count();

        $this->update([
            'total_parcels' => $total,
            'assigned_parcels' => $assigned,
            'unassigned_parcels' => max($total - $assigned, 0)
        ]);
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'active':
                return '<span class="badge badge-pill badge-primary">Active</span>';
            case 'completed':
                return '<span class="badge badge-pill badge-success">Completed</span>';
            case 'cancelled':
                return '<span class="badge badge-pill badge-danger">Cancelled</span>';
            default:
                return '<span class="badge badge-pill badge-secondary">Unknown</span>';
        }
    }

    /**
     * Get session progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        if (($this->total_parcels ?? 0) == 0) {
            return 0;
        }
        return round(($this->assigned_parcels ?? 0) / max($this->total_parcels, 1) * 100, 2);
    }

    /**
     * Get unassigned parcels count
     */
    public function getUnassignedParcelsAttribute()
    {
        // prefer stored column if present
        if (array_key_exists('unassigned_parcels', $this->attributes)) {
            return $this->attributes['unassigned_parcels'];
        }
        return max(($this->total_parcels ?? 0) - ($this->assigned_parcels ?? 0), 0);
    }
}
