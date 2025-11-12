<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">
        <i class="fa fa-phone"></i>
        Follow Up –  {{ $enquiry->name ?? 'Unknown' }}
    </h4>
</div>

@if($enquiry)

<form id="followupForm" action="{{ route('admin.enquiries.followup.store', $enquiry->id) }}" method="POST">
    @csrf

    <div class="modal-body">

        <!-- Current Status Display -->
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            <strong>Current Status:</strong>
            <span class="label label-primary">{{ ucfirst($enquiry->status ?? 'N/A') }}</span>
            @if($enquiry->last_follow_up_date)
                <br>
                <small>Last follow-up: {{ $enquiry->last_follow_up_date->format('M j, Y') }}</small>
            @endif
        </div>

        <!-- Follow-up Date -->
        <div class="form-group">
            <label for="followup_date" class="control-label">Follow-up Date & Time <span class="text-danger">*</span></label>
            <input type="datetime-local" name="followup_date" id="followup_date"
                   class="form-control @error('followup_date') is-invalid @enderror"
                   value="{{ old('followup_date', now()->format('Y-m-d\TH:i')) }}"
                   max="{{ now()->format('Y-m-d\TH:i') }}">
            @error('followup_date')
                <div class="help-block text-danger">{{ $message }}</div>
            @enderror
            <span class="help-block">
                When did this follow-up occur? You can select past dates up to current time.
            </span>
        </div>

        <!-- Notes -->
        <div class="form-group">
            <label for="notes" class="control-label">Follow-up Notes <span class="text-danger">*</span></label>
            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                      rows="4" placeholder="Enter details of the conversation, email, or meeting..." required>{{ old('notes') }}</textarea>
            @error('notes')
                <div class="help-block text-danger">{{ $message }}</div>
            @enderror
            <span class="help-block">
                Describe what was discussed and any outcomes or next steps.
            </span>
        </div>

        <!-- Next Follow-up Date -->
        <div class="form-group">
            <label for="next_follow_up_date" class="control-label">Next Follow-up Date</label>
            <input type="date" name="next_follow_up_date" id="next_follow_up_date"
                   class="form-control @error('next_follow_up_date') is-invalid @enderror"
                   value="{{ old('next_follow_up_date', $enquiry->next_follow_up_date ? $enquiry->next_follow_up_date->format('Y-m-d') : '') }}"
                   min="{{ date('Y-m-d') }}">
            @error('next_follow_up_date')
                <div class="help-block text-danger">{{ $message }}</div>
            @enderror
            <span class="help-block">
                Set the date for the next follow-up. Leave empty if no further follow-up needed.
            </span>
        </div>

        <!-- Status Update (Optional) -->
        <div class="form-group">
            <label for="update_status" class="control-label">Update Status (Optional)</label>
            <select name="update_status" id="update_status" class="form-control">
                <option value="">Keep current status ({{ ucfirst($enquiry->status ?? '') }})</option>
                <option value="active">Active</option>
                <option value="closed">Closed</option>
                <option value="converted">Converted</option>
                <option value="lost">Lost</option>
            </select>
            <span class="help-block">
                Optionally update the enquiry status based on this follow-up.
            </span>
        </div>

        <!-- Follow-up History -->
        @if($enquiry->followups && $enquiry->followups->count() > 0)
        <div class="form-group">
            <hr>
            <label class="control-label">Recent Follow-up History</label>
            <div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                @foreach($enquiry->followups->sortByDesc('followup_date')->take(5) as $followup)
                <div class="followup-item" style="padding: 5px 0; border-bottom: 1px solid #eee; font-size: 12px;">
                    <strong>{{ $followup->followup_date->format('M j, Y g:i A') }}:</strong>
                    {{ Str::limit($followup->description, 80) }}
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Save Follow-up
        </button>
    </div>
</form>

@else
    <div class="modal-body">
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i>
            No enquiry data available.
        </div>
    </div>
@endif
