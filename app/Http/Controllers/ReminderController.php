<?php

namespace App\Http\Controllers;

use App\Models\Priority;
use App\Models\Reminder;
use App\Models\ReminderCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $filterDate = $request->input('filter_date', Carbon::today()->toDateString());

        $auth_id = Auth::user()->id;
        $priority = Priority::all();
        $reminder_categories = ReminderCategory::with('reminders')->where('user_id', $auth_id)->get();
        $reminders = Reminder::whereDate('due_date', $filterDate)
            ->orderBy('created_at', 'desc')->with('reminder_category')->get();
        return view('reminders.index', compact('reminders', 'reminder_categories', 'priority'));
    }

    public function create()
    {
        return view('reminders.create', compact('reminder_category', 'priority'));
    }

    public function store(Request $request)
    {


        $request->validate([
            'title' => 'required|string|max:255',
            'repeat' => 'boolean',
            'type' => 'required|in:today,daily,weekly,monthly',
            'interval' => $request->input('type') !== 'today' ? 'nullable|numeric|min:1' : '',
            'due_date' => $request->input('type') === 'today' ? '' : 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:due_date',
            'completed' => 'sometimes|boolean',
            'weekdays' => 'nullable|string',
            'monthdays' => 'nullable|string',
        ]);

        $auth_id = Auth::user()->id;

        $reminder_category = $request->category;
        $priority = $request->priority;

        // dd($reminder_category);
        if ($reminder_category == null) {
            $reminder_category = ReminderCategory::firstOrCreate(
                ['user_id' => $auth_id, 'name' => 'Uncategorized']
            )->id;
        }
        if ($priority == null) {
            $priority = Priority::where('id', 2)->firstOrFail()->id;
        }


        $reminderData = [
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'repeat' => $request->has('repeat'),
            'weekdays' => $request->weekdays, // This will be a comma-separated string
            'monthdays' => $request->monthdays, // This will also be a comma-separated string
            'interval' => $request->interval,
            'due_date' => $request->due_date,
            'end_date' => $request->end_date,
            'reminder_category_id' => $reminder_category,
            'priority_id' => $priority,
            'completed' => $request->has('completed') ? $request->completed : false,
        ];
        
        // Logic to handle 'type' field based on 'repeat'
        if (!$reminderData['repeat']) {
            // If repeat is off, set 'type' to null or a default value
            $reminderData['type'] = null; // Or another default value
        } else {
            // Handle the 'type' as usual
            $reminderData['type'] = $request->type;
        }

        // Set the due_date to today if the type is 'today'
        if ($request->type === 'today') {
            $reminderData['due_date'] = Carbon::today()->toDateString();
        } else {
            // Calculate due_date based on type, interval, and end_date
            // Assuming calculateDueDate is a method on the Reminder model that calculates the due date
            $reminderData['due_date'] = (new Reminder())->calculateDueDate($reminderData);
        }

        $reminderData['user_id'] = $auth_id;

        Reminder::create($reminderData);


        return redirect()->route('reminders.index')->with('success', 'Reminder created successfully');
    }

    public function updateCompletionStatus(Request $request, $id)
    {

        $reminder = Reminder::findOrFail($id);
        $status = $request->input('isCompleted');
        if ($status == "true") {
            $status = true;
        } else {
            $status = false;
        }
        $reminder->completed = $status;
        $reminder->save();

        return response()->json(['message' => 'Status updated successfully']);
    }
}
