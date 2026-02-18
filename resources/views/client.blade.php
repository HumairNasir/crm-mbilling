@extends('layouts.backend')
@section('content')
<style>
    .modal-section-title { font-size: 14px; font-weight: bold; color: #555; margin-top: 15px; margin-bottom: 5px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
    .action-icons img, .action-icons svg { width: 18px; cursor: pointer; margin-left: 5px; }
    .sales-rep-badge { background: #e3f2fd; color: #0d47a1; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
    
    /* View Modal Specific Styles */
    .view-row { margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
    .view-label { font-weight: 600; color: #666; font-size: 13px; }
    .view-value { color: #333; font-weight: 500; font-size: 14px; }
    .sub-amount { color: #155724; font-weight: 800; font-size: 16px; background: #d4edda; padding: 2px 8px; border-radius: 4px; }
</style>

<div class="content-main dental-office-content">
    <h3>Clients</h3>
    <div class="dental-office-parent">
        <div class="search-main search-employee">
            <form action="{{ route('clients') }}" method="GET" style="display:flex; align-items:center; width:100%;">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search by office, doctor, sales rep, area..." class="dental-office-search">
                <img src="../images/search.svg" alt="" onclick="this.closest('form').submit()" style="cursor:pointer;">
            </form>
        </div>
        @if(!Auth::user()->hasRole('SalesRepresentative'))
        <div class="add-dental-button">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addClientModal">Add Client</button>
        </div>
        @endif
    </div>

    <div class="dental-table-main">
        <table class="dental-office-table">
            <thead class="dental-table-header">
            <tr>
                <td class="dp-none">#</td>
                <td>Doctor's Office Name</td>
                <td>Doctor Name</td> 
                <td>Sales Rep</td> 
                <td>Email</td>
                <td class="dp-none">Phone</td>
                <td class="dp-none">Status</td>
                <td>Action</td>
            </tr>
            </thead>
            <tbody>
            @forelse($clients as $index => $client)
                <tr>
                    <td class="dp-none">{{ $clients->firstItem() + $index }}</td>
                    <td class="dental-office-name-img">
                        <img src="../images/img.svg" alt="">
                        <div>
                            <h6 style="margin-bottom:0;">{{ $client->name }}</h6>
                        </div>
                    </td>
                    
                    <td>{{ $client->contact_person ?? '-' }}</td>

                    <td>
                        @if($client->salesRep)
                            <span class="sales-rep-badge">{{ $client->salesRep->name }}</span>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>

                    <td class="email-button">
                        <a href="mailto:{{ $client->email }}"><button><img src="../images/email.svg" alt="">{{ $client->email }}</button></a>
                    </td>
                    <td class="dp-none"><span>{{ $client->phone ?? '-' }}</span></td>
                    <td class="dp-none"><span>{{ $client->status }}</span></td>
                    <td class="action-icons">
                        <svg onclick="viewClient({{ $client->id }})" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#007bff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye" ><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>

                        @if(!Auth::user()->hasRole('SalesRepresentative'))
                            <img style="background: #e0e0e0; padding: 5px; border-radius: 4px;" src="../images/pencil.svg" alt="Edit" title="Edit" onclick="editClient({{ $client->id }})">
                            
                            <a href="{{ route('clients.delete', $client->id) }}" onclick="return confirm('Are you sure you want to delete this client?');">
                                <img src="../images/trash.svg" style="background: #e0e0e0; padding: 5px; border-radius: 4px;" alt="Delete" title="Delete">
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center">No Clients Found</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="main-pagination">{{ $clients->links() }}</div>
</div>

<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Add Client</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
      <form action="{{ route('clients.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="form-office-main">
                <div>
                    <div class="modal-section-title">Client Details</div>
                    <label>Client Name <span class="text-danger">*</span></label><input type="text" name="name" required>
                    <label>Doctor Name</label><input type="text" name="contact_person">
                    <label>Email <span class="text-danger">*</span></label><input type="email" name="email" required>
                    <label>Phone</label><input type="text" name="phone">
                    
                    <label>Monthly Subscription ($)</label>
                    <input type="number" name="subscription_amount" step="0.01" placeholder="0.00" class="form-control">

                    <label>Status</label>
                    <select name="status" class="form-control"><option value="Active">Active</option><option value="Inactive">Inactive</option></select>
                </div>
                <div>
                    <div class="modal-section-title">Assign Location</div>
                    <label>Region</label>
                    <select name="region" id="client_region" class="form-control" @if(!Auth::user()->hasRole('CountryManager')) style="pointer-events: none; background: #e9ecef;" readonly @endif>
                        <option value="">Select Region</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" {{ (Auth::user()->region_id == $region->id) ? 'selected' : '' }}>{{ $region->name }}</option>
                        @endforeach
                    </select>
                    <label class="mt-2">Area</label>
                    <select name="area" id="client_area" class="form-control" @if(Auth::user()->hasRole('AreaManager')) style="pointer-events: none; background: #e9ecef;" readonly @endif>
                        <option value="">Select Region First</option>
                        @if(Auth::user()->hasRole('RegionalManager'))
                            @foreach($areas as $area)<option value="{{ $area->id }}">{{ $area->name }}</option>@endforeach
                        @endif
                        @if(Auth::user()->hasRole('AreaManager'))
                            <option value="{{ Auth::user()->state_id }}" selected>{{ Auth::user()->state->name ?? 'My Area' }}</option>
                        @endif
                    </select>
                    <label class="mt-2">Dental Office <span class="text-danger">*</span></label>
                    <select name="dental_office_id" id="client_office" class="form-control" required>
                        <option value="">Select Area First</option>
                        @if(Auth::user()->hasRole('AreaManager'))
                            @foreach($dental_offices as $office)<option value="{{ $office->id }}">{{ $office->name }}</option>@endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer form-office-modal-footer"><button type="submit" class="btn btn-primary">Save</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editClientModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Edit Client</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
      <form id="editClientForm" method="POST">
        @csrf
        <div class="modal-body">
            <div class="form-office-main">
                <div>
                    <div class="modal-section-title">Client Details</div>
                    <label>Client Name <span class="text-danger">*</span></label><input type="text" name="name" id="edt_name" required> 
                    <label>Doctor Name</label><input type="text" name="contact_person" id="edt_contact_person">
                    <label>Email</label><input type="email" name="email" id="edt_email" required>
                    <label>Phone</label><input type="text" name="phone" id="edt_phone">
                    
                    <label>Monthly Subscription ($)</label>
                    <input type="number" name="subscription_amount" id="edt_subscription_amount" step="0.01" class="form-control">

                    <label>Status</label>
                    <select name="status" id="edt_status" class="form-control">
                        <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <div class="modal-section-title">Location</div>
                    <label>Region</label>
                    <select name="region" id="edt_region" class="form-control" @if(!Auth::user()->hasRole('CountryManager')) style="pointer-events: none; background: #e9ecef;" readonly @endif>
                        @foreach($regions as $region)<option value="{{ $region->id }}">{{ $region->name }}</option>@endforeach
                    </select>
                    <label class="mt-2">Area</label>
                    <select name="area" id="edt_area" class="form-control" @if(Auth::user()->hasRole('AreaManager')) style="pointer-events: none; background: #e9ecef;" readonly @endif></select>
                    <label class="mt-2">Dental Office</label>
                    <select name="dental_office_id" id="edt_office" class="form-control" required></select>
                </div>
            </div>
        </div>
        <div class="modal-footer form-office-modal-footer"><button type="submit" class="btn btn-primary">Update</button></div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #f8f9fa;">
        <h5 class="modal-title">Client Details</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" id="viewClientBody">
          <div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    
    // --- ADD MODAL LOGIC ---
    $('#client_region').change(function() {
        var id = $(this).val();
        $('#client_area').html('<option>Loading...</option>');
        $.get('/get-areas/'+id, function(data){
            $('#client_area').html('<option value="">Select Area</option>');
            $.each(data, function(i, v){ $('#client_area').append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
    $('#client_area').change(function() {
        var id = $(this).val();
        $('#client_office').html('<option>Loading...</option>');
        $.get('/get-offices-by-area/'+id, function(data){
            $('#client_office').html('<option value="">Select Office</option>');
            $.each(data, function(i, v){ $('#client_office').append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });

    // --- EDIT MODAL LOGIC (Cascading) ---
    $('#edt_region').change(function() {
        var id = $(this).val();
        $.get('/get-areas/'+id, function(data){
            $('#edt_area').html('<option value="">Select Area</option>');
            $.each(data, function(i, v){ $('#edt_area').append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });
    $('#edt_area').change(function() {
        var id = $(this).val();
        $.get('/get-offices-by-area/'+id, function(data){
            $('#edt_office').html('<option value="">Select Office</option>');
            $.each(data, function(i, v){ $('#edt_office').append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });

    // --- SUBMIT EDIT FORM ---
    $('#editClientForm').submit(function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $.post(url, $(this).serialize(), function(response){
            location.reload();
        });
    });
});

// --- OPEN EDIT MODAL ---
function editClient(id) {
    $.get('/clients/' + id + '/edit', function(response) {
        var client = response.client;
        
        // 1. Fill Basic Fields
        $('#edt_name').val(client.name);
        $('#edt_contact_person').val(client.contact_person);
        $('#edt_email').val(client.email);
        $('#edt_phone').val(client.phone);
        $('#edt_status').val(client.status);
        $('#edt_subscription_amount').val(client.subscription_amount); // Populate Subscription
        $('#editClientForm').attr('action', '/clients/update/' + id);

        // 2. Set Region
        $('#edt_region').val(response.region_id);

        // 3. Populate Areas (Pre-filled from server)
        $('#edt_area').empty();
        $.each(response.valid_areas, function(i, v) {
            var selected = (v.id == response.state_id) ? 'selected' : '';
            $('#edt_area').append('<option value="'+v.id+'" '+selected+'>'+v.name+'</option>');
        });

        // 4. Populate Offices (Pre-filled from server)
        $('#edt_office').empty();
        $.each(response.valid_offices, function(i, v) {
            var selected = (v.id == client.dental_office_id) ? 'selected' : '';
            $('#edt_office').append('<option value="'+v.id+'" '+selected+'>'+v.name+'</option>');
        });

        $('#editClientModal').modal('show');
    });
}

// --- OPEN VIEW DETAILS MODAL (NEW) ---
function viewClient(id) {
    $('#viewClientModal').modal('show');
    $('#viewClientBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');

    // We reuse the edit route to get data because it returns the Client model with relationships
    $.get('/clients/' + id + '/edit', function(response) {
        var c = response.client;
        var officeName = (c.dental_office) ? c.dental_office.name : '-';
        var regionName = (c.dental_office && c.dental_office.region) ? c.dental_office.region.name : '-';
        var areaName = (c.dental_office && c.dental_office.state) ? c.dental_office.state.name : '-';
        
        // Format Subscription Amount
        var subAmount = c.subscription_amount ? '$' + parseFloat(c.subscription_amount).toFixed(2) : '$0.00';

        var html = `
            <div class="row">
                <div class="col-md-6 view-row">
                    <div class="view-label">Client Name</div>
                    <div class="view-value">${c.name}</div>
                </div>
                <div class="col-md-6 view-row">
                    <div class="view-label">Doctor Name</div>
                    <div class="view-value">${c.contact_person || '-'}</div>
                </div>
                <div class="col-md-6 view-row">
                    <div class="view-label">Email</div>
                    <div class="view-value"><a href="mailto:${c.email}">${c.email}</a></div>
                </div>
                <div class="col-md-6 view-row">
                    <div class="view-label">Phone</div>
                    <div class="view-value">${c.phone || '-'}</div>
                </div>
                
                <div class="col-12 mt-2 mb-2">
                    <div class="p-3" style="background: #f8f9fa; border-radius: 8px; border: 1px dashed #ced4da;">
                        <div class="view-label text-center mb-1">Monthly Subscription Revenue</div>
                        <div class="text-center"><span class="sub-amount">${subAmount}</span></div>
                    </div>
                </div>

                <div class="col-md-6 view-row mt-2">
                    <div class="view-label">Dental Office</div>
                    <div class="view-value">${officeName}</div>
                </div>
                <div class="col-md-6 view-row mt-2">
                    <div class="view-label">Status</div>
                    <div class="view-value"><span class="badge badge-${c.status == 'Active' ? 'success' : 'secondary'}">${c.status}</span></div>
                </div>
                <div class="col-md-6 view-row">
                    <div class="view-label">Region</div>
                    <div class="view-value">${regionName}</div>
                </div>
                <div class="col-md-6 view-row">
                    <div class="view-label">Area / State</div>
                    <div class="view-value">${areaName}</div>
                </div>
            </div>
        `;
        $('#viewClientBody').html(html);
    });
}
</script>
@endsection