<?php

namespace App\Models\Backend;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class HubTransferCharge extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'from_hub_id',
        'to_hub_id',
        'base_charge',
        'per_km_rate',
        'min_charge',
        'max_charge',
        'weight_factor',
        'status'
    ];

    protected $casts = [
        'base_charge' => 'decimal:2',
        'per_km_rate' => 'decimal:2',
        'min_charge' => 'decimal:2',
        'max_charge' => 'decimal:2',
        'weight_factor' => 'decimal:4',
    ];

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('HubTransferCharge')
            ->logOnly([
                'fromHub.name',
                'toHub.name',
                'base_charge',
                'per_km_rate',
                'min_charge',
                'max_charge',
                'weight_factor',
                'status'
            ])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    /**
     * Get active records
     */
    public function scopeActive($query)
    {
        $query->where('status', Status::ACTIVE);
    }

    /**
     * Get the source hub
     */
    public function fromHub()
    {
        return $this->belongsTo(Hub::class, 'from_hub_id');
    }

    /**
     * Get the destination hub
     */
    public function toHub()
    {
        return $this->belongsTo(Hub::class, 'to_hub_id');
    }

    /**
     * Get status badge
     */
    public function getMyStatusAttribute()
    {
        if ($this->status == Status::ACTIVE) {
            $status = '<span class="badge badge-pill badge-success">' . trans("status." . $this->status) . '</span>';
        } else {
            $status = '<span class="badge badge-pill badge-danger">' . trans("status." . $this->status) . '</span>';
        }
        return $status;
    }

    /**
     * Calculate transfer charge based on distance and weight
     */
    public function calculateCharge($distanceKm, $weight = 1)
    {
        $charge = $this->base_charge + ($distanceKm * $this->per_km_rate * $this->weight_factor * $weight);
        
        // Apply minimum charge
        $charge = max($charge, $this->min_charge);
        
        // Apply maximum charge if set
        if ($this->max_charge) {
            $charge = min($charge, $this->max_charge);
        }
        
        return round($charge, 2);
    }
}
