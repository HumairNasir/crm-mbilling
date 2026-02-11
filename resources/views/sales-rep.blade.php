@extends('layouts.backend')
@section('content')
<style>
    /* MODAL & UI STYLES */
    .error-field { border-color: red; border-width: 1px; }
    
    .form-office-modal-dialog { max-width: 600px; }
    .modal-body { max-height: 70vh; overflow-y: auto; }

    /* CHECKBOX DROPDOWN STYLES */
    .checkbox-dropdown {
        position: relative;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background-color: #fff;
        cursor: pointer;
        padding: 6px 12px;
    }
    .checkbox-dropdown-text {
        color: #495057;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .checkbox-dropdown-list {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #ced4da;
        border-top: none;
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .checkbox-dropdown-list.show { display: block; }
    
    .checkbox-dropdown-item {
        display: block;
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
        margin-bottom: 0;
    }
    .checkbox-dropdown-item:hover { background-color: #f8f9fa; }
    .checkbox-dropdown-item.is-selected { background-color: #d4edda; color: #155724; }
    .checkbox-dropdown-item input { margin-right: 10px; transform: scale(1.2); }

    /* BADGES & HEADERS */
    .region-info-badges { margin-top: 5px; margin-bottom: 15px; font-size: 12px; }
    .region-badge {
        background: #e0e0e0; color: #333; padding: 2px 6px;
        border-radius: 4px; margin-right: 4px; display: inline-block; margin-bottom: 3px;
    }
    .dropdown-header {
        background-color: #e9ecef; font-weight: bold; font-size: 12px;
        padding: 5px 10px; color: #495057; text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* AUTO-PILOT CARD STYLES */
    .auto-pilot-card {
        background: linear-gradient(145deg, #1e293b, #0f172a);
        border-radius: 16px; padding: 15px; position: relative;
        overflow: hidden; color: white; margin-bottom: 30px; width: 80%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05);
    }
    .glow-effect {
        position: absolute; top: -50px; right: -50px; width: 200px; height: 200px;
        background: radial-gradient(circle, rgba(56,189,248,0.2) 0%, rgba(0,0,0,0) 70%); z-index: 0;
    }
    .relative-z { position: relative; z-index: 1; }
    .icon-box {
        background: rgba(255,255,255,0.1); width: 50px; height: 50px;
        border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 15px;
    }
    .card-title { font-size: 20px; font-weight: 700; margin: 0 0 5px 0; color: white; }
    .status-indicator { font-size: 13px; display: flex; align-items: center; gap: 8px; font-weight: 500; }
    .status-indicator.running { color: #4ade80; }
    .status-indicator.waiting { color: #fbbf24; }
    .pulse-dot {
        width: 8px; height: 8px; background-color: #4ade80; border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); animation: pulse-green 2s infinite;
    }
    .static-dot { width: 8px; height: 8px; background-color: #fbbf24; border-radius: 50%; }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(74, 222, 128, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
    }
    .iteration-badge {
        background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 8px 16px; border-radius: 30px; font-size: 13px; color: white;
        display: inline-flex; align-items: center; gap: 8px;
    }
    .iteration-number { background: white; color: #0f172a; font-weight: 800; padding: 2px 8px; border-radius: 10px; font-size: 12px; }
    .premium-table { width: 100%; margin-top: 20px; border-collapse: separate; border-spacing: 0 10px; }
    .premium-table thead th { font-size: 11px; text-transform: uppercase; color: rgba(255,255,255,0.5); padding-bottom: 10px; }
    .premium-table td { vertical-align: middle; padding: 10px 5px; color: white; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .avatar-circle {
        width: 32px; height: 32px; border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #a855f7);
        display: flex; align-items: center; justify-content: center;
        font-weight: bold; font-size: 12px; margin-right: 12px;
    }
</style>

<div class="content-main dental-office-content">
    <h3>Sales Reps</h3>

    <div class="auto-pilot-card">
        <div class="glow-effect"></div>
        <div class="relative-z">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="display: flex; align-items: center;">
                    <div class="icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3H5a2 2 0 0 0-2 2v4m6-6h10a2 2 0 0 1 2 2v4M9 3v18m0 0h10a2 2 0 0 0 2-2V9M9 21H5a2 2 0 0 1-2-2V9m0 0h18"></path></svg>
                    </div>
                    <div>
                        <h4 class="card-title">Auto-Pilot Status Board</h4>
                        @if(isset($activeIteration) && $activeIteration)
                            <div class="status-indicator running"><span class="pulse-dot"></span> System Active</div>
                        @else
                            <div class="status-indicator waiting"><span class="static-dot"></span> Waiting for Next Batch</div>
                        @endif
                    </div>
                </div>
                <div class="iteration-badge">
                    <span style="opacity: 0.7; font-weight: 500;">Current Batch:</span>
                    <span class="iteration-number">{{ $batchId ?? '-' }}</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="premium-table">
                    <thead>
                        <tr>
                            <th style="text-align: left; padding-left: 10px;">Sales Representative</th>
                            <th style="text-align: center;">Pending Tasks</th>
                            <th style="text-align: center;">Assigned Today</th>
                            <th style="text-align: center;">Overall Done</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales_reps as $rep)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center;">
                                    <div class="avatar-circle">{{ substr($rep->name, 0, 1) }}</div>
                                    <div>
                                        <span style="font-weight: 600; font-size: 14px; display: block;">{{ $rep->name }}</span>
                                        <span style="font-size: 11px; opacity: 0.5;">{{ $rep->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                @if($rep->pending_tasks_count > 0)
                                    <span style="color: #fbbf24; font-weight: 700; font-size: 16px;">{{ $rep->pending_tasks_count }}</span>
                                @else
                                    <span style="opacity: 0.2; font-size: 20px;">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span style="color: #38bdf8; font-weight: 700; font-size: 16px;">{{ $rep->assigned_today_count }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span style="color: #4ade80; font-weight: 700; font-size: 16px;">{{ $rep->total_completed_count }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="">
        <div class="dental-office-parent">
            <div class="search-main search-employee">
                <input type="search" name="search" id="search" placeholder="Search..." class="dental-office-search">
                <img src="../images/search.svg" alt="">
            </div>
            
            @if(Auth::user()->roles[0]->name != 'SalesRepresentative')
            <div class="add-dental-button">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSalesRepModal">Add Sales Rep</button>
            </div>
            @endif
        </div>

        <div class="dental-table-main">
            <table class="dental-office-table">
                <thead class="dental-table-header">
                    <tr>
                        <td class="dp-none">#</td>
                        <td>Name</td>
                        <td>Email</td>
                        @if(Auth::user()->hasRole('CountryManager'))
                            <td>Regional Mgr</td>
                            <td>Area Mgr</td>
                        @endif
                        @if(Auth::user()->hasRole('RegionalManager'))
                            <td>Area Mgr</td>
                        @endif
                        <td>Area/State</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales_reps as $index => $rep)
                    <tr>
                        <td class="dp-none">{{ $sales_reps->firstItem() + $index }}</td>
                        <td class="dental-office-name-img">
                            <img src="../images/img.svg" alt="">
                            <h6>{{ $rep->name }}</h6>
                        </td>
                        <td class="email-button">
                            <a href="mailto:{{ $rep->email }}"><button><img src="../images/email.svg" alt="">{{ $rep->email }}</button></a>
                        </td>

                        @if(Auth::user()->hasRole('CountryManager'))
                            <td>{{ $rep->regionalManager->name ?? '-' }}</td>
                            <td>{{ $rep->stateManager->name ?? '-' }}</td>
                        @endif
                        @if(Auth::user()->hasRole('RegionalManager'))
                            <td>{{ $rep->stateManager->name ?? '-' }}</td>
                        @endif

                        <td>
                            @foreach($rep->states as $state)
                                <span class="badge badge-info" style="background-color: #007bff; color: white; padding: 3px 6px; border-radius: 4px; font-size: 10px; margin-right: 2px;">
                                    {{ $state->name }}
                                </span>
                            @endforeach
                        </td>
                        
                        <td class="action-icons">
                            @if(Auth::user()->roles[0]->name != 'SalesRepresentative')
                            <div class='d-flex align-items-center'>
                                <button class="btn edit-sales-rep-btn" type="button" data-id="{{ $rep->id }}">
                                    <img style="background: #e0e0e0; padding: 5px; border-radius: 4px;" src="../images/pencil.svg" alt="Edit" title="Edit">
                                </button>
                                
                                <form action="{{ route('sales_rep.destroy', $rep->id) }}" method="POST" class="delete-form m-0" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn" onclick="return confirm('Are you sure?')">
                                        <img src="../images/trash.svg" alt="Delete" title="Delete" style="background: #e0e0e0; padding: 5px; border-radius: 4px;">
                                    </button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">No Sales Representatives found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="main-pagination">
            {{ $sales_reps->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="addSalesRepModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Sales Rep</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="add-sales-rep-form">
        @csrf
        <div class="modal-body">
          <div class="form-office-main">
            <div>
              <label>Name <span class="text-danger">*</span></label>
              <input type="text" name="name" placeholder="John Doe" required>
              <span class="name-error text-danger"></span>
              
              <label>Email <span class="text-danger">*</span></label>
              <input type="email" name="email" placeholder="john@example.com" required>
              <span class="email-error text-danger"></span>
              
              <label>Password <span class="text-danger">*</span></label>
              <input type="password" name="password" placeholder="********" required>
              <span class="password-error text-danger"></span>

              <label>Phone</label>
              <input type="text" name="phone" placeholder="+123456789">

              <label>Address</label>
              <input type="text" name="address">
            </div>

            <div>
                <label>Regional Manager <span class="text-danger">*</span></label>
                <select name="regional_manager" id="regional_manager" class="form-control"
                    @if(!Auth::user()->hasRole('CountryManager')) style="pointer-events: none; background: #e9ecef;" readonly @endif>
                    
                    <option value="">Select Regional Manager</option>
                    @if(Auth::user()->hasRole('RegionalManager'))
                        <option value="{{ Auth::user()->id }}" selected>{{ Auth::user()->name }}</option>
                    @else
                        @foreach($regional_managers as $regional)
                        <option value="{{ $regional->id }}">{{ $regional->name }}</option>
                        @endforeach
                    @endif
                </select>
                <div class="region-info-badges" id="add-region-badges"></div>
                <span class="regional_manager-error text-danger"></span>

                <label>Area Manager <span class="text-danger">*</span></label>
                <select name="area_manager" id="area_manager" class="form-control"
                    @if(Auth::user()->hasRole('AreaManager')) style="pointer-events: none; background: #e9ecef;" readonly @endif>
                    
                    <option value="">Select Regional Mgr First</option>
                    @if(Auth::user()->hasRole('AreaManager'))
                        <option value="{{ Auth::user()->id }}" selected>{{ Auth::user()->name }}</option>
                    @endif
                </select>
                <span class="area_manager-error text-danger"></span>

                <div class="form-group">
                    <label>Assign Areas (States) <span class="text-danger">*</span></label>
                    <div class="checkbox-dropdown" id="sales-area-dropdown">
                        <div class="checkbox-dropdown-text">Select Area Manager First...</div>
                        <div class="checkbox-dropdown-list" id="sales-area-list"></div>
                    </div>
                    <select name="areas[]" id="real-sales-area" multiple style="display:none;"></select>
                    <span class="areas-error text-danger"></span>
                </div>

            </div>
          </div>
        </div>
        <div class="modal-footer form-office-modal-footer">
          <button type="submit" class="btn btn-primary">Save Sales Rep</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editSalesRepModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Sales Rep</h5>
        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <form id="edit-sales-rep-form" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-office-main">
            <div>
              <label>Name</label>
              <input type="text" name="name" id="edit_name" required>
              <label>Email</label>
              <input type="email" name="email" id="edit_email" required>
              <label>Phone</label>
              <input type="text" name="phone" id="edit_phone">
              <label>Address</label>
              <input type="text" name="address" id="edit_address">
            </div>
            <div>
               <label>Regional Manager</label>
               <select name="regional_manager" id="edit_regional_manager" class="form-control" style="pointer-events: none; background: #e9ecef;" readonly></select>
               <div class="region-info-badges" id="edit-region-badges"></div>

               <label>Area Manager (Boss)</label>
               <select name="area_manager" id="edit_area_manager" class="form-control" style="pointer-events: none; background: #e9ecef;" readonly></select>

               <label>Assign Areas (States)</label>
               <div class="checkbox-dropdown" id="edit-sales-area-dropdown">
                   <div class="checkbox-dropdown-text">Loading...</div>
                   <div class="checkbox-dropdown-list" id="edit-sales-area-list"></div>
               </div>
               <select name="areas[]" id="edit-real-sales-area" multiple style="display:none;"></select>
               
               <label style="margin-top:15px;">New Password (Optional)</label>
               <input type="password" name="password" placeholder="********">
            </div>
          </div>
        </div>
        <div class="modal-footer form-office-modal-footer">
          <button type="submit" class="btn btn-primary update-btn">Update Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    
    // --- HELPER FUNCTIONS ---
    function showLoader() { $('#loader-overlay').show(); }
    function hideLoader() { $('#loader-overlay').hide(); }
    function clearErrors() { $('.text-danger').text(''); }

    // --- CHECKBOX LOGIC ---
    $(document).on('click', '.checkbox-dropdown', function(e) {
        $('.checkbox-dropdown-list').not($(this).find('.checkbox-dropdown-list')).removeClass('show');
        $(this).find('.checkbox-dropdown-list').toggleClass('show');
        e.stopPropagation();
    });
    $(document).on('click', function() { $('.checkbox-dropdown-list').removeClass('show'); });
    $(document).on('click', '.checkbox-dropdown-list', function(e) { e.stopPropagation(); });

    $(document).on('change', '.area-checkbox', function() {
        var dropdown = $(this).closest('.checkbox-dropdown');
        var hiddenSelect = dropdown.siblings('select[multiple]');
        var textDisplay = dropdown.find('.checkbox-dropdown-text');
        var labelItem = $(this).closest('.checkbox-dropdown-item'); 
        var value = $(this).val();
        var text = $(this).parent().text().trim();
        
        if($(this).is(':checked')) {
            if (hiddenSelect.find("option[value='"+value+"']").length == 0) {
                hiddenSelect.append(new Option(text, value, true, true));
            }
            labelItem.addClass('is-selected');
        } else {
            hiddenSelect.find("option[value='"+value+"']").remove();
            labelItem.removeClass('is-selected');
        }
        updateText(hiddenSelect, textDisplay);
    });

    function updateText(select, display) {
        var count = select.find('option').length;
        if(count === 0) display.text("Select States...");
        else display.text(count + " states selected");
    }

    // --- FETCH MANAGER REGIONS (BADGES) ---
    function fetchRegionBadges(managerId, targetId) {
        var container = $(targetId);
        container.empty();
        if(!managerId) return;

        $.get('/get-manager-regions/' + managerId, function(regions) {
            if(regions.length > 0) {
                regions.forEach(function(r) {
                    container.append('<span class="region-badge">'+r.name+'</span>');
                });
            } else {
                container.html('<small class="text-muted">No regions assigned.</small>');
            }
        });
    }

    // --- FETCH GROUPED STATES FUNCTION ---
    function fetchManagerStates(managerId, targetListId, selectedIds = [], salesRepId = null) {
        var url = '/get-manager-states/' + managerId + '/' + (salesRepId ? salesRepId : 'null');
        var listContainer = $(targetListId);
        var hiddenSelect = listContainer.closest('.checkbox-dropdown').siblings('select[multiple]');
        var textDisplay = listContainer.siblings('.checkbox-dropdown-text');

        listContainer.empty();
        hiddenSelect.empty();
        textDisplay.text("Loading...");

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                var data = response.grouped_states;
                var hasStates = false;

                if(!data || Object.keys(data).length === 0) {
                    textDisplay.text("No available states");
                    listContainer.append('<div style="padding:10px; color:#999;">No states available</div>');
                    return;
                }

                $.each(data, function(regionName, states) {
                    hasStates = true;
                    // Add Header
                    listContainer.append('<div class="dropdown-header">' + regionName + '</div>');

                    $.each(states, function(i, state) {
                        var isChecked = selectedIds.some(id => id == state.id) ? 'checked' : '';
                        var colorClass = isChecked ? 'is-selected' : '';

                        var item = `
                            <label class="checkbox-dropdown-item ${colorClass}">
                                <input type="checkbox" class="area-checkbox" value="${state.id}" ${isChecked}>
                                ${state.name}
                            </label>`;
                        listContainer.append(item);

                        if(isChecked) {
                            hiddenSelect.append(new Option(state.name, state.id, true, true));
                        }
                    });
                });

                if(hasStates) updateText(hiddenSelect, textDisplay);
                else textDisplay.text("No available states");
            },
            error: function(xhr) {
                console.error("Fetch error:", xhr);
                textDisplay.text("Error loading states");
            }
        });
    }

    // --- ADD MODAL EVENTS ---
    $('#regional_manager').change(function() {
        var rmId = $(this).val();
        
        fetchRegionBadges(rmId, '#add-region-badges');

        $('#area_manager').html('<option value="">Loading...</option>');
        $('#sales-area-list').empty();
        $('#sales-area-dropdown .checkbox-dropdown-text').text("Select Area Manager First...");

        if(rmId) {
            $.get('/get-area-managers/' + rmId, function(data) {
                $('#area_manager').html('<option value="">Select Area Manager</option>');
                $.each(data, function(index, user) {
                    $('#area_manager').append('<option value="'+user.id+'">'+user.name+'</option>');
                });
            });
        }
    });

    $('#area_manager').change(function() {
        var amId = $(this).val();
        if(amId) {
            fetchManagerStates(amId, '#sales-area-list'); 
        } else {
            $('#sales-area-list').empty();
            $('#sales-area-dropdown .checkbox-dropdown-text').text('Select Area Manager First...');
        }
    });

    // --- EDIT MODAL EVENTS ---
    $('.edit-sales-rep-btn').click(function() {
        var userId = $(this).data('id');
        $('#edit-sales-area-list').empty();
        $('#edit-real-sales-area').empty(); 
        $('#edit-sales-area-dropdown .checkbox-dropdown-text').text("Loading...");

        $.ajax({
            url: '/edit-salesrep/' + userId,
            type: 'GET',
            success: function(data) {
                $('#edit_name').val(data.name);
                $('#edit_email').val(data.email);
                $('#edit_phone').val(data.phone);
                $('#edit_address').val(data.address);
                $('.update-btn').data('id', data.user_id);

                $('#edit_regional_manager').html('<option value="'+data.regional_manager_id+'" selected>Current Regional Mgr</option>');
                fetchRegionBadges(data.regional_manager_id, '#edit-region-badges');

                $('#edit_area_manager').html('<option value="'+data.state_manager_id+'" selected>Current Area Mgr</option>');

                if (data.state_manager_id) {
                    fetchManagerStates(data.state_manager_id, '#edit-sales-area-list', data.area_ids, data.user_id);
                }

                $('#editSalesRepModal').modal('show');
            },
            error: function() { alert("Error loading user data"); }
        });
    });

    // --- SUBMIT FORMS ---
    $('#add-sales-rep-form').submit(function(e) {
        e.preventDefault();
        clearErrors();
        $.ajax({
            url: "{{ route('sales_rep.store') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function() { location.reload(); },
            error: function(xhr) {
                if(xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(k, v) { $('.'+k.split('.')[0]+'-error').text(v[0]); });
                }
            }
        });
    });

    $('.update-btn').click(function(e) {
        e.preventDefault();
        clearErrors();
        var userId = $(this).data('id');
        $.ajax({
            url: "/sales_rep/" + userId,
            method: "PUT",
            data: $('#edit-sales-rep-form').serialize(),
            success: function() { location.reload(); },
            error: function(xhr) { console.error(xhr); alert("Error updating."); }
        });
    });

    // --- AUTO-INIT ---
    @if(Auth::user()->hasRole('RegionalManager'))
        var loggedInRmId = "{{ Auth::user()->id }}";
        fetchRegionBadges(loggedInRmId, '#add-region-badges');
    @endif
    
    @if(Auth::user()->hasRole('AreaManager'))
        var loggedInAmId = "{{ Auth::user()->id }}";
        fetchManagerStates(loggedInAmId, '#sales-area-list');
    @endif
});
</script>
@endsection