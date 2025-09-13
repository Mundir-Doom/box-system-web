<?php

namespace App\Repositories\CollectionSession;

use App\Models\Backend\CollectionSession;
use Carbon\Carbon;

class CollectionSessionRepository implements CollectionSessionInterface
{
    protected $model;

    public function __construct(CollectionSession $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->with(['collectionPeriod'])
                          ->orderBy('collection_sessions.collection_date', 'desc')
                          ->orderBy('collection_sessions.id', 'desc')
                          ->get();
    }

    public function getForDate($date)
    {
        return $this->model->with(['collectionPeriod'])
                          ->forDate($date)
                          ->get();
    }

    public function getActive()
    {
        return $this->model->with(['collectionPeriod'])
                          ->active()
                          ->orderBy('collection_sessions.collection_date', 'desc')
                          ->get();
    }

    public function getCurrent()
    {
        return $this->model->with(['collectionPeriod'])
                          ->active()
                          ->forDate(Carbon::today())
                          ->get();
    }

    public function findById($id)
    {
        return $this->model->with(['collectionPeriod', 'parcelAssignments.parcel', 'parcelAssignments.deliveryMan'])
                          ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $session = $this->model->findOrFail($id);
        $session->update($data);
        return $session;
    }

    public function startSession($periodId, $date = null)
    {
        $date = $date ?: Carbon::today();
        
        // Check if session already exists for this period and date
        $existingSession = $this->model->where('collection_period_id', $periodId)
                                      ->whereDate('collection_date', $date)
                                      ->first();

        if ($existingSession) {
            return $existingSession->startCollection();
        }

        // Create new session
        $session = $this->create([
            'collection_period_id' => $periodId,
            'collection_date' => Carbon::parse($date)->format('Y-m-d'),
            'status' => 'active',
            'started_at' => Carbon::now()
        ]);

        return $session;
    }

    public function completeSession($id)
    {
        $session = $this->model->findOrFail($id);
        return $session->completeCollection();
    }

    public function getSessionsByDateRange($startDate, $endDate)
    {
        return $this->model->with(['collectionPeriod'])
                          ->whereBetween('collection_sessions.collection_date', [$startDate, $endDate])
                          ->orderBy('collection_sessions.collection_date', 'desc')
                          ->get();
    }

    public function getSessionsWithCounts()
    {
        // Use stored counters on model
        return $this->model->with(['collectionPeriod'])
                          ->orderBy('collection_sessions.collection_date', 'desc')
                          ->get();
    }
}
