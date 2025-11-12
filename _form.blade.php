<!-- Enquiry Modal Form -->
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">
        {{ isset($enquiry) ? 'Edit Enquiry' : 'Add New Enquiry' }}
    </h4>
</div>

<form id="enquiryForm" method="POST"
      action="{{ isset($enquiry) ? route('admin.enquiries.update', $enquiry->id) : route('admin.enquiries.store') }}">
    @csrf
    @if(isset($enquiry))
        @method('PUT')
    @endif

    <div class="modal-body">
        <div class="row">
            <!-- Name -->
            <div class="col-md-6 form-group">
                <label>Name *</label>
                <input type="text" name="name" id="name" class="form-control"
                       value="{{ old('name', $enquiry->name ?? '') }}" required>
                <div class="invalid-feedback"></div>
            </div>

            <!-- Phone -->
            <div class="col-md-6 form-group">
                <label>Phone *</label>
                <input type="text" name="phone" id="phone" class="form-control"
                       value="{{ old('phone', $enquiry->phone ?? '') }}" required>
                <div class="invalid-feedback"></div>
            </div>

            <!-- Email -->
            <div class="col-md-6 form-group">
                <label>Email</label>
                <input type="email" name="email" id="email" class="form-control"
                       value="{{ old('email', $enquiry->email ?? '') }}">
                <div class="invalid-feedback"></div>
            </div>

            <!-- Address -->
            <div class="col-md-6 form-group">
                <label>Address</label>
                <input type="text" name="address" id="address" class="form-control"
                       value="{{ old('address', $enquiry->address ?? '') }}">
                <div class="invalid-feedback"></div>
            </div>

            <!-- Description -->
            <div class="col-md-12 form-group">
                <label>Initial Enquiry Notes</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $enquiry->description ?? '') }}</textarea>
                <div class="invalid-feedback"></div>
            </div>

            <!-- Enquiry Date -->
            <div class="col-md-6 form-group">
                <label>Enquiry Date *</label>
                <input type="date" name="enquiry_date" id="enquiry_date" class="form-control"
                       value="{{ old('enquiry_date', isset($enquiry) ? $enquiry->enquiry_date?->format('Y-m-d') : date('Y-m-d')) }}" required>
                <div class="invalid-feedback"></div>
            </div>

            <!-- Next Follow-up -->
            <div class="col-md-6 form-group">
                <label>Next Follow-up Date</label>
                <input type="date" name="next_follow_up_date" id="next_follow_up_date" class="form-control"
                       value="{{ old('next_follow_up_date', isset($enquiry) ? $enquiry->next_follow_up_date?->format('Y-m-d') : '') }}"
                       min="{{ date('Y-m-d') }}">
                <div class="invalid-feedback"></div>
            </div>

            <!-- Status -->
            <div class="col-md-6 form-group">
                <label>Status *</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="">Select Status</option>
                    @foreach(['active','closed','converted','lost'] as $status)
                        <option value="{{ $status }}" {{ old('status', $enquiry->status ?? '') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">
            {{ isset($enquiry) ? 'Update Enquiry' : 'Save Enquiry' }}
        </button>
    </div>
</form>
