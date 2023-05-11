<?php

require_once 'Rota.php';
require_once 'Shift.php';
require_once 'SingleManning.php';

class SingleManningCalculator
{
    public function calculateSingleManning(Rota $rota): SingleManning
    {
        $singleManning = new SingleManning();

        // Iterate over each day of the week
        for ($day = 1; $day <= 7; $day++) {
            $date = new DateTime("next Monday +".($day - 1)." days");
            $totalMinutes = 0;

            // Check each shift for the current day
            foreach ($rota->getShifts() as $shift) {
                if ($this->isShiftOnDay($shift, $date)) {
                    $totalMinutes += $this->calculateShiftMinutes($shift);
                }
            }

            $singleManning->addMinutes($date->format('Y-m-d'), $totalMinutes);
        }
        return $singleManning;
    }
    private function isShiftOnDay(Shift $shift, DateTime $date): bool
    {
        return $shift->getStartTime()->format('Y-m-d') === $date->format('Y-m-d');
    }

    private function calculateShiftMinutes(Shift $shift): int
    {
        $start = $shift->getStartTime();
        $end = $shift->getEndTime();
        $breaks = $shift->getBreaks();

        $shiftInterval = $end->diff($start);
        foreach ($breaks as $break) {
            $breakStart = new DateTime($break[0]);
            $breakEnd = new DateTime($break[1]);

            // Check if the break overlaps with the shift
            if ($breakStart < $breakEnd) {
                // Calculate the overlapping interval
                $overlapStart = max($start, $breakStart);
                
                $overlapEnd = min($end, $breakEnd);
                $overlapInterval = $overlapEnd->diff($overlapStart);

                // Subtract the overlapping interval from the shift interval
                $shiftInterval = $this->subtractInterval($shiftInterval, $overlapInterval);
            }
        }

        $minutes = ($shiftInterval->h * 60) + $shiftInterval->i;
        return $minutes;
    }

    private function subtractInterval(DateInterval $interval1, DateInterval $interval2): DateInterval
    {
        $start = new DateTimeImmutable();
        $end = $start->add($interval1);
        
        $end = $end->sub($interval2);
        return $start->diff($end);
    }
}