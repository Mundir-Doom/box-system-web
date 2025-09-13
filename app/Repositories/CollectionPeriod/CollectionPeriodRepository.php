<?php

namespace App\Repositories\CollectionPeriod;

use App\Models\Backend\CollectionPeriod;
use Carbon\Carbon;

class CollectionPeriodRepository implements CollectionPeriodInterface
{
    protected $model;

    public function __construct(CollectionPeriod $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->orderBy('start_time')->get();
    }

    public function getActive()
    {
        return $this->model->active()->orderBy('start_time')->get();
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $period = $this->findById($id);
        $period->update($data);
        return $period;
    }

    public function delete($id)
    {
        $period = $this->findById($id);
        return $period->delete();
    }

    public function getPeriodsForTime($time)
    {
        return $this->model->active()->forTime($time)->get();
    }

    public function getCurrentActivePeriods()
    {
        $now = Carbon::now()->format('H:i:s');
        return $this->model->active()
                          ->whereTime('start_time', '<=', $now)
                          ->whereTime('end_time', '>=', $now)
                          ->get();
    }

    public function toggle($id)
    {
        $period = $this->findById($id);
        $period->update(['is_active' => !$period->is_active]);
        return $period;
    }
}
