<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Enums\ParcelAssignmentStatus;

class ParcelCollectionAssignment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'parcel_id',
        'collection_session_id',
        'collected_at',
        'delivery_man_id',
        'assigned_at',
        'assignment_status',
        'priority',
        'notes'
    ];

    protected $casts = [
        'collected_at' => 'datetime',
        'assigned_at' => 'datetime',
        'priority' => 'integer'
    ];

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('ParcelCollectionAssignment')
            ->logOnly(['parcel_id', 'collection_session_id', 'delivery_man_id', 'assignment_status'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    /**
     * Scope to get unassigned parcels
     */
    public function scopeUnassigned($query)
    {
        return $query->where('assignment_status', 'collected')
                    ->whereNull('delivery_man_id');
    }

    /**
     * Scope to get assigned parcels
     */
    public function scopeAssigned($query)
    {
        return $query->where('assignment_status', 'assigned')
                    ->whereNotNull('delivery_man_id');
    }

    /**
     * Scope to get assignments by delivery man
     */
    public function scopeByDeliveryMan($query, $deliveryManId)
    {
        return $query->where('delivery_man_id', $deliveryManId);
    }

    /**
     * Get the parcel
     */
    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }

    /**
     * Get the collection session
     */
    public function collectionSession()
    {
        return $this->belongsTo(CollectionSession::class);
    }

    /**
     * Get the delivery man
     */
    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class);
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        switch ($this->assignment_status) {
            case 'collected':
                return '<span class="badge badge-pill badge-warning">Collected</span>';
            case 'assigned':
                return '<span class="badge badge-pill badge-info">Assigned</span>';
            case 'out_for_delivery':
                return '<span class="badge badge-pill badge-primary">Out for Delivery</span>';
            case 'delivered':
                return '<span class="badge badge-pill badge-success">Delivered</span>';
            default:
                return '<span class="badge badge-pill badge-secondary">Unknown</span>';
        }
    }

    /**
     * Get priority badge
     */
    public function getPriorityBadgeAttribute()
    {
        switch ($this->priority) {
            case 1:
                return '<span class="badge badge-pill badge-danger">High</span>';
            case -1:
                return '<span class="badge badge-pill badge-secondary">Low</span>';
            case 0:
            default:
                return '<span class="badge badge-pill badge-info">Normal</span>';
        }
    }

    /**
     * Check if parcel is assigned
     */
    public function isAssigned()
    {
        return !is_null($this->delivery_man_id) && $this->assignment_status !== 'collected';
    }

    /**
     * Assign to delivery man
     */
    public function assignTo($deliveryManId, $priority = 0)
    {
        $this->update([
            'delivery_man_id' => $deliveryManId,
            'assigned_at' => now(),
            'assignment_status' => 'assigned',
            'priority' => $priority
        ]);

        return $this;
    }

    /**
     * Unassign from delivery man
     */
    public function unassign()
    {
        $this->update([
            'delivery_man_id' => null,
            'assigned_at' => null,
            'assignment_status' => 'collected',
            'priority' => 0
        ]);

        return $this;
    }
}
