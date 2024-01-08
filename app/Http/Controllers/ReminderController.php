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
        $reminders = Reminder::with('reminder_category')->get();

        $filterDate = $request->has('filter_date') ? Carbon::parse($request->input('filter_date')) : Carbon::today();
        $reminders = Reminder::whereDate('due_date', '<=', $filterDate)
            ->where(function ($query) use ($filterDate) {
                $query->whereDate('end_date', '>=', $filterDate)
                    ->orWhereNull('end_date');
            })
            ->get()
            ->filter(function ($reminder) use ($filterDate) {
                // Parse due_date as a Carbon instance if it's not already
                $dueDate = $reminder->due_date instanceof Carbon ? $reminder->due_date : new Carbon($reminder->due_date);

                // Calculate the difference in days
                $daysDifference = $dueDate->diffInDays($filterDate, false);
                if (empty($reminder->interval) || $reminder->interval == 0) {
                    return false; // or true, depending on how you want to handle this case
                }


                // Return true if the difference is an even number (0, 2, 4, ...)
                return $daysDifference % $reminder->interval == 0;
            });
        // dd($reminders);
        // // Filter based on interval
        // $reminders = $reminders->filter(function ($reminder) use ($filterDate) {
        //     $interval = $reminder->interval;
        //     if ($interval === 0) {
        //         return false; // Ignore this reminder or handle it as per your application's logic
        //     }

        //     $startDate = Carbon::parse($reminder->due_date);

        //     // Calculate the number of days between the start date and the filter date
        //     $daysDifference = $startDate->diffInDays($filterDate);
        //     dd($daysDifference);
        //     // Check if the reminder falls on the filter date based on the interval
        //     // return $daysDifference % $interval === 0;
        // });

        return view('reminders.index', compact('reminders', 'reminder_categories', 'priority'));
    }

    public function create()
    {
        return view('reminders.create', compact('reminder_category', 'priority'));
    }

    public function store(Request $request)
    {
        // dd($request);
        $request->validate([
            'title' => 'required|string|max:255',
            'repeat' => 'boolean',
            'type' => $request->has('repeat') ? 'required|in:daily,weekly,monthly' : '',
            'interval' => $request->has('repeat') ? 'required|numeric|min:1' : '',
            'end_date' => $request->has('repeat') ? 'nullable|date|after_or_equal:today' : '',
            'weekdays' => 'nullable|string', // Only applicable for weekly reminders
            'monthdays' => 'nullable|string', // Only applicable for monthly reminders
            'completed' => 'sometimes|boolean',
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
            'repeat' => $request->has('repeat'),
            'weekdays' => $request->weekdays, // This will be a comma-separated string
            'monthdays' => $request->monthdays, // This will also be a comma-separated string
            'due_date' => $request->due_date,
            'reminder_category_id' => $reminder_category,
            'priority_id' => $priority,
            'completed' => $request->has('completed') ? $request->completed : false,
        ];

        // Additional data for repeating reminders
        if ($request->has('repeat')) {
            $reminderData['type'] = $request->type;
            $reminderData['interval'] = $request->interval;
            $reminderData['end_date'] = $request->end_date;

            if ($request->type === 'weekly') {
                $reminderData['weekdays'] = $request->weekdays;
            } elseif ($request->type === 'monthly') {
                $reminderData['monthdays'] = $request->monthdays;
            }
        } else {
            $reminderData['end_date'] = $request->single_date;
        }


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
        // dd(($reminderData));

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
