<?php

class Shift
{
    const BLACK_WIDOW = 'Black Widow';
    const THOR = 'Thor';
    const WOLVERINE = 'Wolverine';
    const GAMORA = 'Gamora';
    
    protected $employee;
    protected $startTime;
    protected $endTime;
    protected $breaks;
    
    public function __construct($employee, DateTime $startTime, DateTime $endTime, array $breaks = [])
    {
        $this->employee = $employee;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->breaks = $breaks;
    }

    public function getEmployee()
    {
        return $this->employee;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }
    
    public function getBreaks()
    {
        return $this->breaks;
    }

}