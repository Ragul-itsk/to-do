<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'reminder_category_id',
        'priority_id',
        'title',
        'description',
        'repeat',
        'type',
        'interval',
        'end_date',
        'due_date',
        'weekdays',
        'monthdays',
        'completed'
    ];

    public function calculateDueDate()
    {
        $now = Carbon::now();

        switch ($this->type) {
            case 'daily':
                $dueDate = $now->addDays($this->interval);
                break;
            case 'weekly':
                $dueDate = $now->addWeeks($this->interval);
                break;
            case 'monthly':
                $dueDate = $now->addMonths($this->interval);
                break;
            default:
                $dueDate = $now;
        }

        // Check if an end_date is specified and limit the due date accordingly
        if ($this->end_date && $now->lessThanOrEqualTo(Carbon::parse($this->end_date))) {
            $dueDate = min($dueDate, Carbon::parse($this->end_date));
        }

        return $dueDate;
    }

    public function reminder_category()
    {
        return $this->belongsTo(ReminderCategory::class);
    }
}
