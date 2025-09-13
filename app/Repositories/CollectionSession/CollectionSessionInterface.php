<?php

namespace App\Repositories\CollectionSession;

interface CollectionSessionInterface
{
    public function getAll();
    public function getForDate($date);
    public function getActive();
    public function getCurrent();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function startSession($periodId, $date);
    public function completeSession($id);
    public function getSessionsByDateRange($startDate, $endDate);
    public function getSessionsWithCounts();
}
