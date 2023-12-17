<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function reminders()
{
    return $this->hasMany(Reminder::class, 'reminder_category_id');
}
}
