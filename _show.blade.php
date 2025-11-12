<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">
        <i class="fa fa-eye"></i>
        Enquiry Details - {{ $enquiry->name }}
    </h4>
</div>

<div class="modal-body">
    <!-- Basic Information Card -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Basic Information</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-condensed">
                        <tr>
                            <th width="40%">Name:</th>
                            <td>{{ $enquiry->name }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>
                                <a href="tel:{{ $enquiry->phone }}">{{ $enquiry->phone }}</a>
                            </td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>
                                @if($enquiry->email)
                                    <a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-condensed">
                        <tr>
                            <th width="40%">Enquiry Date:</th>
                            <td>{{ $enquiry->enquiry_date->format('M j, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="status-badge status-{{ $enquiry->status }}">
                                    {{ ucfirst($enquiry->status) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Last Follow-up:</th>
                            <td>
                                @if($enquiry->last_follow_up_date)
                                    {{ $enquiry->last_follow_up_date->format('M j, Y') }}
                                @else
                                    <span class="text-muted">Never</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Next Follow-up:</th>
                            <td>
                                @if($enquiry->next_follow_up_date)
                                    @php
                                        $nextDate = $enquiry->next_follow_up_date;
                                        $today = \Carbon\Carbon::today();
                                        $className = $nextDate < $today ? 'text-danger' : ($nextDate->isToday() ? 'text-warning' : '');
                                    @endphp
                                    <span class="{{ $className }}">
                                        {{ $nextDate->format('M j, Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Address -->
            @if($enquiry->address)
            <div class="row">
                <div class="col-md-12">
                    <strong>Address:</strong>
                    <p class="text-muted">{{ $enquiry->address }}</p>
                </div>
            </div>
            @endif

            <!-- Description -->
            @if($enquiry->description)
            <div class="row">
                <div class="col-md-12">
                    <strong>Initial Enquiry Notes:</strong>
                    <div class="well well-sm" style="margin-bottom: 0;">
                        {{ $enquiry->description }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Follow-up History Card -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Follow-up History
                <span class="badge">{{ $enquiry->followups->count() }}</span>
            </h3>
        </div>
        <div class="panel-body" style="max-height: 300px; overflow-y: auto;">
            @if($enquiry->followups->count() > 0)
                @foreach($enquiry->followups as $followup)
                <div class="followup-item" style="padding: 10px; border-bottom: 1px solid #eee; {{ !$loop->last ? 'margin-bottom: 10px;' : '' }}">
                    <div class="row">
                        <div class="col-md-2">
                            <strong>{{ $followup->followup_date->format('M j, Y') }}</strong>
                            <br>
                            <small class="text-muted">{{ $followup->followup_date->format('g:i A') }}</small>
                        </div>
                        <div class="col-md-10">
                            <p style="margin-bottom: 5px;">{{ $followup->description }}</p>
                            <small class="text-muted">
                                Added by: {{ $followup->user->name ?? 'System' }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="text-center text-muted">
                    <i class="fa fa-history fa-2x" style="margin-bottom: 10px;"></i>
                    <p>No follow-ups recorded yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-info followup-btn"
            data-url="{{ route('admin.enquiries.followup', $enquiry->id) }}">
        <i class="fa fa-phone"></i> Add Follow-up
    </button>
    <button type="button" class="btn btn-primary edit-btn"
            data-url="{{ route('admin.enquiries.edit', $enquiry->id) }}">
        <i class="fa fa-edit"></i> Edit Enquiry
    </button>
</div>
