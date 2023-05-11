<?php

require_once 'Shift.php';

class SingleManning
{
    protected $minutes = [];

    public function __construct(array $minutes = [])
    {
        $this->minutes = $minutes;
    }

    public function addMinutes($date, $minutes)
    {
        $this->minutes[$date] = $minutes;
    }

    // public function getMinutes()
    // {
    //     return $this->minutes;
    // }
}