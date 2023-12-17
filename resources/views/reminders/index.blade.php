@extends('layouts.app')

@section('content')
    <link href="{{ asset('assets/css/reminder.css') }}" rel="stylesheet">
    <!-- Plus Icon Button in Bottom Right Corner -->
    <button type="button" class="btn btn-primary position-fixed bottom-0 end-0 m-4" data-bs-toggle="modal"
        data-bs-target="#reminderModal" style="z-index: 1031;">
        <i class="fas fa-plus"></i>
    </button>
    <section class="vh-100 gradient-custom">
        <div class="container py-5">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">


                    <div class="card bg-white">
                        <div class="card-body">
                            <form method="get" action="{{ route('reminders.index') }}" class="mb-4">
                                <div class="input-group">
                                    <input type="date" name="filter_date" class="form-control"
                                        value="{{ request()->input('filter_date', \Carbon\Carbon::today()->toDateString()) }}">
                                    <button type="submit" class="btn btn-info text-white">Filter</button>
                                </div>
                            </form>
                            <nav>
                                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                        aria-selected="true">All</button>
                                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-profile" type="button" role="tab"
                                        aria-controls="nav-profile" aria-selected="false">Active</button>
                                    <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-contact" type="button" role="tab"
                                        aria-controls="nav-contact" aria-selected="false">Completed</button>
                                </div>
                            </nav>
                            <div class="tab-content p-3 border" id="nav-tabContent">
                                <div class="tab-pane fade active show" id="nav-home" role="tabpanel"
                                    aria-labelledby="nav-home-tab">
                                    <!-- List all reminders -->
                                    @include('reminders.reminder-list', ['reminders' => $reminders])
                                </div>
                                <div class="tab-pane fade" id="nav-profile" role="tabpanel"
                                    aria-labelledby="nav-profile-tab">
                                    <!-- List active (not completed) reminders -->
                                    @include('reminders.reminder-list', [
                                        'reminders' => $reminders->where('completed', false),
                                    ])
                                </div>
                                <div class="tab-pane fade" id="nav-contact" role="tabpanel"
                                    aria-labelledby="nav-contact-tab">
                                    <!-- List completed reminders -->
                                    @include('reminders.reminder-list', [
                                        'reminders' => $reminders->where('completed', true),
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
        </div>
    </section>
    <!-- Reminder Modal -->
    <div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true"
        style="padding-top:2rem">
        <div class="modal-dialog">
            <div class="modal-content bg-white">
                <div class="modal-header">
                    <h5 class="modal-title" id="reminderModalLabel">New Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <!-- Form to create a new reminder -->
                    <form method="post" action="{{ route('reminders.store') }}"
                        class="ustify-content-center align-items-center mb-4">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <label for="description">Task Title <span class="mandatory-field">* </span>:</label>
                                <input type="text" name="title" class="form-control" />
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="description">Description:</label>
                                <textarea name="description" class="form-control" id="description"></textarea>
                            </div>
                        </div>
                        <div class="tab-container mt-2 mb-2">
                            <!-- Tabs for Daily, Weekly, Monthly -->
                            <nav>
                                <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                                    <button class="nav-link active" id="nav-daliy-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-daily" type="button" role="tab"
                                        aria-controls="nav-daily" aria-selected="true">Daily</button>
                                    <button class="nav-link" id="nav-weekly-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-weekly" type="button" role="tab"
                                        aria-controls="nav-weekly" aria-selected="false">Weekly</button>
                                    <button class="nav-link" id="nav-monthly-tab" data-bs-toggle="tab"
                                        data-bs-target="#nav-monthly" type="button" role="tab"
                                        aria-controls="nav-monthly" aria-selected="false">Monthly</button>
                                </div>
                            </nav>

                            <!-- Tab panes -->
                            <div class="tab-content">
                                {{-- Dail Pane --}}
                                <div class="tab-pane fade show active" id="nav-daily" role="tabpanel"
                                    aria-labelledby="daily-tab">
                                    <div class="repeat-section">
                                        <label for="daily-interval">Repeat</label>
                                        <select id="daily-interval" class="form-control">
                                            @for ($i = 1; $i <= 30; $i++)
                                                <option value="{{ $i }}">Every {{ $i }} Day(s)
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="end-date-section">
                                        <label for="daily-end-date">End date</label>
                                        <input type="date" id="daily-end-date" class="form-control">
                                    </div>
                                </div>
                                <!-- Weekly pane -->
                                <div id="nav-weekly" class="tab-pane fade">
                                    <div class="weekdays-selector">
                                        <!-- Weekdays buttons -->
                                        @foreach (['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                                            <button type="button"
                                                class="btn btn-outline-primary weekday-btn">{{ $day }}</button>
                                        @endforeach
                                    </div>
                                    <div class="interval-selector">
                                        <label for="weekly-interval">Repeat</label>
                                        <select id="weekly-interval" class="form-control">
                                            <!-- Options for interval -->
                                            @for ($i = 1; $i <= 48; $i++)
                                                <option value="{{ $i }}">Every {{ $i }} Week(s)
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="end-date-selector">
                                        <label for="weekly-end-date">End date</label>
                                        <input type="date" id="weekly-end-date" class="form-control">
                                    </div>
                                </div>
                                {{-- Monthly Pane --}}
                                <div class="tab-pane fade" id="nav-monthly" role="tabpanel"
                                    aria-labelledby="monthly-tab">
                                    <div class="calendar-section">
                                        <!-- Simple representation of a calendar where users can click to select dates -->
                                        <div class="calendar-grid">
                                            @for ($i = 1; $i <= 31; $i++)
                                                <div class="calendar-day">{{ $i }}</div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="repeat-section">
                                        <label for="monthly-interval">Repeat</label>
                                        <select id="monthly-interval" class="form-control">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">Every {{ $i }} Month(s)
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="end-date-section">
                                        <label for="monthly-end-date">End date</label>
                                        <input type="date" id="monthly-end-date" class="form-control">
                                    </div>
                                </div>
                                <!-- ... Daily and Monthly panes -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="category">Category:</label>
                                <select name="category" id="category" class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($reminder_categories as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="priority">Priority:</label>
                                <select name="priority" id="priority" class="form-control">
                                    <option value="">Select</option>
                                    @foreach ($priority as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info form-control mt-4 text-white">Add</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save Reminder</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log("inside DOM");
            var reminderItems = document.querySelectorAll('.reminder-item');

            reminderItems.forEach(function(item) {
                item.addEventListener('click', function(event) {
                    // Check if the click occurred on the checkbox itself
                    if (!event.target.closest('.form-check-input')) {
                        // Find the associated checkbox and trigger its click event
                        var checkbox = item.querySelector('.form-check-input');
                        if (checkbox) {
                            checkbox.click();
                        }
                    }
                });
            });

            let checkboxes = document.querySelectorAll('.form-check-input');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    let reminderId = this.getAttribute('data-reminder-id');
                    let isCompleted = this.checked;

                    // Obtain the CSRF token
                    let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    // Make an AJAX request to update completion status
                    updateCompletionStatus(reminderId, isCompleted, csrfToken);
                });
            });

            function updateCompletionStatus(reminderId, isCompleted, csrfToken) {
                let xhr = new XMLHttpRequest();
                xhr.open('POST', '/update-completion-status/' + reminderId);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken); // Include CSRF token in the headers

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Update the UI as needed
                        let reminderTextElement = document.querySelector(
                            `.form-check-input[data-reminder-id="${reminderId}"] + .reminder-text`
                        );
                        // console.log(reminderTextElement1);
                        if (reminderTextElement) {
                            if (isCompleted) {
                                reminderTextElement.classList.add('text-decoration-line-through');
                            } else {
                                reminderTextElement.classList.remove('text-decoration-line-through');
                            }
                        }
                    }
                };

                xhr.send(JSON.stringify({
                    isCompleted: isCompleted
                }));
            }


            // Handle the click event on weekday buttons
            document.querySelectorAll('.weekday-btn').forEach(function(dayBtn) {
                dayBtn.addEventListener('click', function() {
                    this.classList.toggle('selected'); // Toggle a class to indicate selection
                });
            });


            // Handle the selection of dates on the calendar
            document.querySelectorAll('.calendar-day').forEach(function(day) {
                day.addEventListener('click', function() {
                    this.classList.toggle('selected'); // Toggle a class to indicate selection
                });
            });

            // Toggle the end date input based on the checkbox
            var endDateToggle = document.getElementById('monthly-end-date-toggle');
            var endDateInput = document.getElementById('monthly-end-date');

            endDateToggle.addEventListener('change', function() {
                endDateInput.disabled = !this.checked;
                if (!this.checked) {
                    endDateInput.value = ''; // Clear the date if disabled
                }
            });

        });
    </script>
@endsection
