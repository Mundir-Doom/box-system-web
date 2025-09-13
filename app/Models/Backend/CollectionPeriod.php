<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class CollectionPeriod extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'is_active',
        'description'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i:s',
        'end_time' => 'datetime:H:i:s',
        'is_active' => 'boolean'
    ];

    /**
     * Activity Log
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('CollectionPeriod')
            ->logOnly(['name', 'start_time', 'end_time', 'is_active'])
            ->setDescriptionForEvent(fn(string $eventName) => "{$eventName}");
    }

    /**
     * Scope to get only active collection periods
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get periods that include a specific time
     */
    public function scopeForTime($query, $time)
    {
        return $query->whereTime('start_time', '<=', $time)
                    ->whereTime('end_time', '>=', $time);
    }

    /**
     * Get collection sessions for this period
     */
    public function collectionSessions()
    {
        return $this->hasMany(CollectionSession::class);
    }

    /**
     * Check if this period is currently active
     */
    public function isCurrentlyActive()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now()->format('H:i:s');
        return $now >= $this->start_time && $now <= $this->end_time;
    }

    /**
     * Get current collection session for today
     */
    public function getCurrentSession()
    {
        return $this->collectionSessions()
                   ->where('collection_date', Carbon::today())
                   ->where('status', 'active')
                   ->first();
    }

    /**
     * Get next upcoming session
     */
    public function getUpcomingSession()
    {
        return $this->collectionSessions()
                   ->where('collection_date', '>=', Carbon::today())
                   ->where('status', 'active')
                   ->orderBy('collection_date')
                   ->first();
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute()
    {
        return Carbon::parse($this->start_time)->format('h:i A') . ' - ' . 
               Carbon::parse($this->end_time)->format('h:i A');
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute()
    {
        if ($this->is_active) {
            return '<span class="badge badge-pill badge-success">Active</span>';
        }
        return '<span class="badge badge-pill badge-secondary">Inactive</span>';
    }
}
