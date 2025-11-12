<div class="followup-history">
      @if(isset($enquiry) && $enquiry && $enquiry->followups && $enquiry->followups->count() > 0)
        <div class="timeline">
            @foreach($enquiry->followups as $followup)
                <div class="timeline-item mb-3">
                    <div class="timeline-badge bg-primary"></div>
                    <div class="timeline-content card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <h6 class="card-title mb-1">
                                    <i class="fa fa-clock-o me-1"></i>
                                    {{ $followup->followup_date->format('M j, Y g:i A') }}
                                </h6>
                                <small class="text-muted">
                                    @if($followup->user)
                                        by {{ $followup->user->name }}
                                    @else
                                        by System
                                    @endif
                                </small>
                            </div>
                            <p class="card-text mb-0">{{ $followup->description }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="fa fa-info-circle me-2"></i>
            No follow-up history yet. Use the "Follow Up" button to add the first follow-up.
        </div>
    @endif
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
}
.timeline-badge {
    position: absolute;
    left: -30px;
    top: 10px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}
.timeline-content {
    margin-left: 0;
}
</style>
