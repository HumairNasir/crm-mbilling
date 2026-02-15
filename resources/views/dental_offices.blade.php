@extends('layouts.backend')
@section('content')
<style>
  .error-field { border-color: red; }
  .text-muted { font-size: 12px; color: #6c757d; }
  .view-detail-row { margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
  .view-detail-row:last-child { border-bottom: none; }
  /* Ensure form columns look clean */
  /* Professional Modal Layout */
.form-office-main { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 25px; 
    padding: 10px;
}

.form-office-main > div { 
    flex: 1; 
    min-width: 300px; /* Ensures columns don't get too skinny */
}

.form-office-main label {
    font-weight: 600;
    margin-bottom: 5px;
    display: block;
    color: #333;
}

/* Force all inputs to be full width and consistent height */
.form-office-main .form-control,
.form-office-main input[type="text"],
.form-office-main input[type="email"],
.form-office-main input[type="number"],
.form-office-main select,
.form-office-main textarea {
    width: 100% !important;
    display: block;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ced4da;
}
</style>

<div class="content-main dental-office-content">
  {{-- PASTE THIS ERROR BLOCK HERE --}}
    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 20px; padding: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px;">
            <h4><i class="fa fa-exclamation-triangle"></i> Submission Failed!</h4>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
  <h3>Doctors' offices</h3>
  <div class="">
    <div class="dental-office-parent">
      <div class="search-main search-employee">
        <form action="{{ route('dental_offices') }}" method="GET" style="display:flex; align-items:center; width:100%;">
          <input type="search" name="search" value="{{ request('search') }}" placeholder="Search office, region, area..." class="dental-office-search">
          <img src="../images/search.svg" alt="" onclick="this.closest('form').submit()" style="cursor:pointer;">
        </form>
      </div>

      @if(!Auth::user()->hasRole('SalesRepresentative'))
      <div class="add-dental-button">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDentalOfficeModal">Add Doctor Office</button>
      </div>
      @endif
    </div>

    <div class="dental-table-main">
      <table class="dental-office-table">
        <thead class="dental-table-header">
          <tr>
            <td class="dp-none">#</td>
            <td>Office Name</td>
            <td>Email</td>
            <td class="dp-none">Region</td>
            <td class="dp-none">Area (State)</td>
            <td class="dp-none">Status</td>
            <td class="dp-none">Sales Person</td>
            <td>Action</td>
          </tr>
        </thead>
        <tbody>
          @forelse($dentalOffices as $index => $office)
          <tr>
            <td class="dp-none">{{ $dentalOffices->firstItem() + $index }}</td>
            <td class="dental-office-name-img">
              <img src="../images/img.svg" alt="">
              <div>
                <h6 style="margin-bottom:0;">{{ $office->name ?? 'N/A' }}</h6>
                @if($office->dr_name) <small class="text-muted">{{ $office->dr_name }}</small> @endif
              </div>
            </td>
            <td class="email-button">
              <a href="mailto:{{ $office->email }}"><button><img src="../images/email.svg" alt="">{{ Str::limit($office->email, 20) }}</button></a>
            </td>
            <td class="dp-none"><span>{{ $office->region->name ?? '-' }}</span></td>
            <td class="dp-none"><span>{{ $office->state->name ?? '-' }}</span></td>
            <td class="dp-none">
                <span class="badge @if($office->receptive == 'HOT') badge-danger @elseif($office->receptive == 'WARM') badge-warning @else badge-secondary @endif">
                    {{ $office->receptive ?? 'COLD' }}
                </span>
            </td>
            <td class="dp-none"><span>{{ $office->salesRep->name ?? 'Unassigned' }}</span></td>

            <td class="action-icons">
              <div class="d-flex align-items-center">
                <svg data-toggle="modal" data-target="#viewModal{{ $office->id }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="width:20px; height:20px; margin-right:5px; cursor:pointer;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>

                @if(!Auth::user()->hasRole('SalesRepresentative'))
                <img style="background: #e0e0e0; padding: 5px; border-radius: 4px; cursor:pointer; margin-right:5px;" src="../images/pencil.svg" alt="Edit" title="Edit" onclick="editDentalOffice({{ $office->id }})">
                <a href="{{ route('dental_offices.delete', $office->id) }}" onclick="return confirm('Delete this office?')">
                  <img src="../images/trash.svg" style="background: #e0e0e0; padding: 5px; border-radius: 4px;" alt="Delete" title="Delete">
                </a>
                @endif
              </div>
            </td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center">No doctors' offices found</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="main-pagination">{{ $dentalOffices->links() }}</div>
  </div>
</div>

<div class="modal fade" id="addDentalOfficeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Doctor Office</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form action="{{ route('dental_offices.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-office-main">
            <div>
              <label>Office Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" required>
              
              <label class="mt-2">Doctor Name</label>
              <input type="text" name="dr_name" class="form-control">
              
              <label class="mt-2">Email</label>
              <input type="email" name="email" class="form-control">
              
              <!-- <label class="mt-2">Phone</label>
              <input type="text" name="phone" class="form-control"> -->
              <label class="mt-2">Phone (US Format) <span class="text-muted">(e.g., 555-012-3456)</span></label>
              <input type="text" name="phone" id="add_phone" class="form-control" placeholder="___-___-____" maxlength="12">

              <input type="hidden" name="country" value="United States">

              <label class="mt-2">Contact Person</label>
              <input type="text" name="contact_person" class="form-control">

              <label class="mt-2">Address</label>
              <input type="text" name="address" class="form-control">
              
              <label class="mt-2">Description</label>
              <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div>
              <label>Select Region <span class="text-danger">*</span></label>
              <select name="region_id" id="add_region_id" class="form-control" required>
                <option value="">Select Region</option>
                @foreach($regions as $region)
                  <option value="{{ $region->id }}">{{ $region->name }}</option>
                @endforeach
              </select>

              <label class="mt-2">Select State <span class="text-danger">*</span></label>
              <select name="state_id" id="add_state_id" class="form-control" required>
                <option value="">Select Region First</option>
              </select>

              <label class="mt-2">Sales Rep (Optional)</label>
              <select name="sales_rep" id="add_sales_rep" class="form-control">
                <option value="">Unassigned</option>
              </select>

              <label class="mt-2">Receptive Status</label>
              <select name="receptive" class="form-control">
                <option value="COLD">COLD</option>
                <option value="WARM">WARM</option>
                <option value="HOT">HOT</option>
              </select>

              <label class="mt-2">Territory ID</label>
              <input type="number" name="territory" class="form-control">

              <div class="row mt-2">
                  <div class="col-6"><label>Contact Date</label><input type="date" name="contact_date" class="form-control"></div>
                  <div class="col-6"><label>Follow-up</label><input type="date" name="follow_up_date" class="form-control"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer form-office-modal-footer">
          <button type="submit" class="btn btn-primary">Save Office</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editDentalOfficeModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Doctor Office</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="editForm" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-office-main">
            <div>
              <label>Office Name</label>
              <input type="text" name="name" id="edt_name" class="form-control" required>
              <label class="mt-2">Doctor Name</label>
              <input type="text" name="dr_name" id="edt_dr_name" class="form-control">
              <label class="mt-2">Email</label>
              <input type="email" name="email" id="edt_email" class="form-control">
              <label class="mt-2">Phone</label>
              <input type="text" name="phone" id="edt_phone" class="form-control">
              <label class="mt-2">Contact Person</label>
              <input type="text" name="contact_person" id="edt_contact" class="form-control">
              <label class="mt-2">Address</label>
              <input type="text" name="address" id="edt_address" class="form-control">
              <label class="mt-2">Description</label>
              <textarea name="description" id="edt_description" class="form-control" rows="2"></textarea>
            </div>
            <div>
              <label>Region</label>
              <select name="region_id" id="edt_region_id" class="form-control">
                @foreach($regions as $region)
                  <option value="{{ $region->id }}">{{ $region->name }}</option>
                @endforeach
              </select>
              <label class="mt-2">State</label>
              <select name="state_id" id="edt_state_id" class="form-control"></select>
              <label class="mt-2">Sales Rep</label>
              <select name="sales_rep" id="edt_sales_rep" class="form-control"></select>
              <label class="mt-2">Receptive Status</label>
              <select name="receptive" id="edt_receptive" class="form-control">
                <option value="COLD">COLD</option>
                <option value="WARM">WARM</option>
                <option value="HOT">HOT</option>
              </select>
              <label class="mt-2">Territory ID</label>
              <input type="number" name="territory" id="edt_territory" class="form-control">
              <div class="row mt-2">
                  <div class="col-6"><label>Contact Date</label><input type="date" name="contact_date" id="edt_contact_date" class="form-control"></div>
                  <div class="col-6"><label>Follow-up</label><input type="date" name="follow_up_date" id="edt_follow_up_date" class="form-control"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer form-office-modal-footer">
          <button type="submit" class="btn btn-primary">Update Office</button>
        </div>
      </form>
    </div>
  </div>
</div>

@foreach($dentalOffices as $office)
<div class="modal fade" id="viewModal{{ $office->id }}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Details: {{ $office->name }}</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div class="view-detail-row"><strong>Doctor:</strong> {{ $office->dr_name ?? 'N/A' }}</div>
            <div class="view-detail-row"><strong>Contact:</strong> {{ $office->contact_person ?? 'N/A' }}</div>
            <div class="view-detail-row"><strong>Phone:</strong> {{ $office->phone ?? 'N/A' }}</div>
            <div class="view-detail-row"><strong>Status:</strong> {{ $office->receptive ?? 'COLD' }}</div>
          </div>
          <div class="col-md-6">
            <div class="view-detail-row"><strong>Region:</strong> {{ $office->region->name ?? '-' }}</div>
            <div class="view-detail-row"><strong>Area:</strong> {{ $office->state->name ?? '-' }}</div>
            <div class="view-detail-row"><strong>Rep:</strong> {{ $office->salesRep->name ?? 'Unassigned' }}</div>
            <div class="view-detail-row"><strong>Follow-up:</strong> {{ $office->follow_up_date ?? '-' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endforeach

<script>
    // --- GLOBAL FUNCTIONS (Must be outside document.ready) ---

    function updateEditStates(regionId, selectedStateId) {
        $('#edt_state_id').html('<option value="">Loading...</option>');
        if (regionId) {
            $.get('/get-states-by-region/' + regionId, function(res) {
                $('#edt_state_id').html('<option value="">Select State</option>');
                $.each(res.states, function(i, v) {
                    var sel = (v.id == selectedStateId) ? 'selected' : '';
                    $('#edt_state_id').append('<option value="' + v.id + '" ' + sel + '>' + v.name + '</option>');
                });
            });
        }
    }

    function updateEditReps(stateId, selectedRepId) {
        $('#edt_sales_rep').html('<option value="">Loading...</option>');
        if (stateId) {
            $.get('/get-sales-reps/' + stateId, function(res) {
                $('#edt_sales_rep').html('<option value="">Unassigned</option>');
                $.each(res.reps, function(i, v) {
                    var sel = (v.id == selectedRepId) ? 'selected' : '';
                    $('#edt_sales_rep').append('<option value="' + v.id + '" ' + sel + '>' + v.name + '</option>');
                });
            });
        }
    }

    function editDentalOffice(id) {
        $.get('/dental_offices/' + id + '/edit', function(res) {
            var office = res.office;
            $('#edt_name').val(office.name);
            $('#edt_dr_name').val(office.dr_name);
            $('#edt_email').val(office.email);
            $('#edt_phone').val(office.phone);
            $('#edt_contact').val(office.contact_person);
            $('#edt_address').val(office.address);
            $('#edt_description').val(office.description);
            $('#edt_territory').val(office.territory_id);
            $('#edt_receptive').val(office.receptive);
            $('#edt_contact_date').val(office.contact_date);
            $('#edt_follow_up_date').val(office.follow_up_date);

            $('#editForm').attr('action', '/dental_offices/update/' + id);
            
            // Set Region (Trigger states load)
            $('#edt_region_id').val(office.region_id);
            
            // Trigger chained loads for edit
            updateEditStates(office.region_id, office.state_id);
            updateEditReps(office.state_id, office.sales_rep_id);

            $('#editDentalOfficeModal').modal('show');
        });
    }

    // --- DOCUMENT READY (Event Listeners) ---
    $(document).ready(function() {
        
        // 1. ADD MODAL: Region -> State
        $('#add_region_id').change(function() {
            var id = $(this).val();
            $('#add_state_id').html('<option value="">Loading...</option>');
            if (id) {
                $.get('/get-states-by-region/' + id, function(res) {
                    $('#add_state_id').html('<option value="">Select State</option>');
                    $.each(res.states, function(i, v) {
                        $('#add_state_id').append('<option value="' + v.id + '">' + v.name + '</option>');
                    });
                });
            }
        });

        // 2. ADD MODAL: State -> Sales Rep
        $('#add_state_id').change(function() {
            var id = $(this).val();
            $('#add_sales_rep').html('<option value="">Loading...</option>');
            if (id) {
                $.get('/get-sales-reps/' + id, function(res) {
                    $('#add_sales_rep').html('<option value="">Unassigned</option>');
                    $.each(res.reps, function(i, v) {
                        $('#add_sales_rep').append('<option value="' + v.id + '">' + v.name + '</option>');
                    });
                });
            }
        });

        // 3. EDIT MODAL: Change Listeners
        $('#edt_region_id').change(function() {
            var id = $(this).val();
            updateEditStates(id, null);
        });

        $('#edt_state_id').change(function() {
            var id = $(this).val();
            updateEditReps(id, null);
        });

        // 4. PHONE MASK (US Format)
        $('#add_phone, #edt_phone').on('input', function() {
            var val = $(this).val().replace(/\D/g, ''); // Remove non-digits
            if (val.length > 10) val = val.substring(0, 10);
            
            var formatted = val;
            if (val.length > 3 && val.length <= 6) {
                formatted = val.substring(0, 3) + '-' + val.substring(3);
            } else if (val.length > 6) {
                formatted = val.substring(0, 3) + '-' + val.substring(3, 6) + '-' + val.substring(6);
            }
            $(this).val(formatted);
        });

        // 5. UPDATE FORM SUBMIT
        $('#editForm').submit(function(e) {
            e.preventDefault();
            $.post($(this).attr('action'), $(this).serialize(), function() {
                location.reload();
            }).fail(function(xhr) {
                alert('Error: ' + xhr.responseText);
            });
        });

        // Debugging
        $('form[action*="store"]').on('submit', function(e) {
            var formData = $(this).serializeArray();
            console.log("--- Sending Data to Server ---");
            console.table(formData);
        });
    });
</script>
@endsection