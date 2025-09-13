<?php

namespace App\Repositories\CollectionPeriod;

interface CollectionPeriodInterface
{
    public function getAll();
    public function getActive();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getPeriodsForTime($time);
    public function getCurrentActivePeriods();
    public function toggle($id);
}
