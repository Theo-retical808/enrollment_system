<?php

namespace App\Exceptions;

class UnitLoadException extends ValidationException
{
    public function __construct(int $currentUnits, int $attemptedUnits, int $maxUnits = 21)
    {
        $newTotal = $currentUnits + $attemptedUnits;
        $message = "Unit load limit exceeded. Current: {$currentUnits} units, Attempted: {$attemptedUnits} units, New Total: {$newTotal} units. Maximum allowed: {$maxUnits} units.";
        
        parent::__construct(
            $message,
            [],
            [
                'current_units' => $currentUnits,
                'attempted_units' => $attemptedUnits,
                'new_total' => $newTotal,
                'max_units' => $maxUnits,
                'exceeded_by' => $newTotal - $maxUnits,
            ]
        );
        
        $this->errorCode = 'UNIT_LOAD_EXCEEDED';
    }
}
