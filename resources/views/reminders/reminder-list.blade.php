<!-- Reminder List Partial -->
{{-- {{ dd($reminders)}} --}}
<ul class="list-group mb-0">
    @forelse ($reminders as $reminder)
        <li class="list-group-item d-flex align-items-center border-0 mb-2 rounded reminder-item" style="background-color: #f4f6f7;">
            <input class="form-check-input me-2" type="checkbox" value="" aria-label="..." {{ $reminder->completed ? 'checked' : '' }} data-reminder-id="{{ $reminder->id }}">
            <span class="reminder-text {{ $reminder->completed ? 'text-decoration-line-through' : '' }}">
                {{ $reminder->title }} (Category: {{ $reminder->reminder_category->name }})
            </span>
        </li>
    @empty
        <li class="list-group-item">No reminders found</li>
    @endforelse
</ul>
