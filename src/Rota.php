<?php

require_once 'Shift.php';

class Rota
{
    private $shifts = [];

    public function addShift(Shift $shift)
    {
        $this->shifts[] = $shift;
    }

    public function getShifts()
    {
        return $this->shifts;
    }
}