<?php

namespace App\Exports;

use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class DtrExport
{
    protected $records;
    protected $user;
    protected $month;
    protected $year;

    public function __construct($records, $user, $month = null, $year = null)
    {
        $this->records = $records;
        $this->user = $user;
        $this->month = $month ?? now()->month;
        $this->year = $year ?? now()->year;
    }

    public function generate()
    {
        $recordsByDay = $this->records->groupBy(function($record) {
            return Carbon::parse($record->attendance_date)->day;
        });

        $daysData = [];
        $totalUnderTimeMinutes = 0;

        for ($day = 1; $day <= 31; $day++) {
            $dateString = sprintf('%04d-%02d-%02d', $this->year, $this->month, $day);
            
            try {
                $date = Carbon::createFromFormat('Y-m-d', $dateString);
                
                // Skip dates beyond today and dates from other months
                if ($date > now() || $date->month != $this->month) {
                    continue;
                }
            } catch (\Exception $e) {
                continue;
            }

            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            $daysData[$day] = [];
            $record = $recordsByDay->get($day)->first() ?? null;

            if ($record) {
                // Parse time_in and time_out - they could be strings or Carbon instances
                $timeIn = is_string($record->time_in) ? Carbon::createFromFormat('H:i:s', $record->time_in) : $record->time_in;
                $timeOut = is_string($record->time_out) ? Carbon::createFromFormat('H:i:s', $record->time_out) : $record->time_out;

                // AM Arrival time
                if ($timeIn) {
                    $daysData[$day]['am_arrival'] = $timeIn->format('H:i');
                    // For this form, AM departure is typically a full morning session ending at noon
                    $daysData[$day]['am_depart'] = '12:00'; 
                }
                
                // PM Arrival and Departure
                if ($timeOut) {
                    // PM arrival is typically after lunch break (1:00 PM)
                    $daysData[$day]['pm_arrival'] = '13:00';
                    // PM departure
                    $daysData[$day]['pm_depart'] = $timeOut->format('H:i');
                }

                // Calculate undertime if there's incomplete hours
                if ($timeIn && $timeOut) {
                    // Expected work time: 8 hours (8:00 AM to 5:00 PM with 1 hour lunch)
                    $expected_minutes = 480; // 8 hours
                    $actual_minutes = $timeIn->diffInMinutes($timeOut);
                    
                    // Assuming 1 hour lunch break
                    $actual_work_minutes = $actual_minutes - 60;
                    
                    if ($actual_work_minutes < $expected_minutes) {
                        $undertime_minutes = $expected_minutes - $actual_work_minutes;
                        $daysData[$day]['undertime_hours'] = intdiv($undertime_minutes, 60);
                        $daysData[$day]['undertime_minutes'] = $undertime_minutes % 60;
                        $totalUnderTimeMinutes += $undertime_minutes;
                    } else {
                        $daysData[$day]['undertime_hours'] = 0;
                        $daysData[$day]['undertime_minutes'] = 0;
                    }
                }
            }
        }

        $totalHours = intdiv($totalUnderTimeMinutes, 60);
        $totalMinutes = $totalUnderTimeMinutes % 60;

        return View::make('exports.dtr-export', [
            'user' => $this->user,
            'daysData' => $daysData,
            'month' => $this->month,
            'year' => $this->year,
            'totalHours' => $totalHours,
            'totalMinutes' => $totalMinutes,
        ])->render();
    }
}
