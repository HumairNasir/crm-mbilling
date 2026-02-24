@extends('layouts.backend')

@section('content')
<style>
    /* STYLES (Kept existing styles) */
    .task-page-header { margin-bottom: 25px; display: flex; justify-content: space-between; align-items: end; }
    .task-page-title { font-size: 24px; font-weight: 700; color: #2c3e50; margin: 0; }
    .task-page-subtitle { font-size: 14px; color: #6c757d; margin-bottom: 0; }
    .dashboard-card-dark { background: linear-gradient(145deg, #1e293b, #0f172a); border-radius: 16px; padding: 20px; position: relative; overflow: hidden; color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05); min-height: 500px; }
    .glow-effect { position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(56,189,248,0.2) 0%, rgba(0,0,0,0) 70%); z-index: 0; }
    .relative-z { position: relative; z-index: 1; }
    .nav-pills-dark { display: flex; gap: 10px; margin-bottom: 20px; }
    .nav-link-dark { background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; padding: 8px 20px; font-size: 13px; font-weight: 600; transition: all 0.3s; }
    .nav-link-dark:hover { background: rgba(255,255,255,0.1); color: white; }
    .nav-link-dark.active { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-color: transparent; box-shadow: 0 4px 12px rgba(59,130,246,0.4); }
    .badge-count { background: white; color: #2563eb; padding: 2px 6px; border-radius: 10px; font-size: 10px; font-weight: 800; margin-left: 8px; }
    .search-dark-wrapper { position: relative; width: 300px; margin-left: auto; }
    .search-dark-input { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; padding: 10px 15px 10px 35px; font-size: 13px; width: 100%; transition: all 0.2s; }
    .search-dark-input:focus { background: rgba(255,255,255,0.2); outline: none; border-color: rgba(255,255,255,0.3); }
    .search-icon-dark { position: absolute; left: 12px; top: 12px; width: 14px; opacity: 0.5; color: white; }
    .table-dark-custom { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .table-dark-custom thead th { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); padding: 0 15px 10px 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .table-dark-custom tbody tr { background: rgba(255,255,255,0.02); transition: background 0.2s; }
    .table-dark-custom tbody tr:hover { background: rgba(255,255,255,0.05); }
    .table-dark-custom td { padding: 15px; border: none; color: rgba(255,255,255,0.9); font-size: 13px; vertical-align: middle; }
    .table-dark-custom td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-dark-custom td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }
    .btn-icon-dark { background: rgba(255,255,255,0.1); border: none; border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; color: white; transition: all 0.2s; cursor: pointer; }
    .btn-icon-dark:hover { background: rgba(255,255,255,0.2); transform: scale(1.05); }
    .btn-convert-small { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; border: none; border-radius: 6px; padding: 6px 14px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.2s; }
    .btn-convert-small:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(34,197,94,0.3); }
    .btn-mark-done { color: rgba(255,255,255,0.5); font-size: 12px; background: none; border: none; text-decoration: underline; cursor: pointer; transition: color 0.2s; }
    .btn-mark-done:hover { color: #4ade80; }
    .sub-text { font-size: 11px; color: rgba(255,255,255,0.4); display: block; margin-top: 4px; }
    .ai-badge { background: rgba(59,130,246,0.2); color: #93c5fd; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-right: 5px; }
    .rep-scroll-container { display: flex; gap: 15px; overflow-x: auto; padding-bottom: 15px; margin-bottom: 20px; scrollbar-width: thin; scrollbar-color: #4B5563 #f1f5f9;padding-top: 10px; }
    .rep-btn { background: #192436; border: 1px solid #192439; color: #e0e0e0; padding: 10px 20px; border-radius: 50px; white-space: nowrap; cursor: pointer; transition: all 0.3s; font-weight: 600; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    .rep-btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    .rep-btn.active { background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border-color: transparent; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
    .rep-avatar { width: 24px; height: 24px; background: rgba(0,0,0,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; }
    
    /* PAGINATION STYLING */
    .pagination { justify-content: center; margin-top: 20px; }
    .page-item .page-link { background-color: #1e293b; border-color: #334155; color: #cbd5e1; font-size: 12px; padding: 8px 12px; }
    .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; font-weight: 700; }
    .page-item.disabled .page-link { background-color: #0f172a; border-color: #334155; color: #475569; }
    .page-link:hover { background-color: #334155; color: white; border-color: #475569; }
    /* CUSTOM PREMIUM DROPDOWN */
    .custom-rep-select {
        background-color: #1e293b;
        color: #f8fafc;
        border: 1px solid #334155;
        border-radius: 10px; /* Rounded corners for the select box */
        padding: 12px 45px 12px 15px; /* 45px on the right gives room for the arrow */
        width: 100%;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        
        /* Hides the default browser arrow */
        appearance: none; 
        -webkit-appearance: none;
        -moz-appearance: none;
        
        /* Adds a custom, clean SVG arrow */
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center; /* Exactly 15px margin from the right edge */
        background-size: 18px;
        transition: all 0.3s ease;
    }

    /* Glow effect when clicked */
    .custom-rep-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    

    /* Style the dropdown items (Options) */
    .custom-rep-select option {
        background-color: #0f172a; /* Dark background for the dropdown list */
        color: #f8fafc;
        padding: 15px;
        font-weight: 500;
    }
</style>

<div class="content-main">
    <div class="task-page-header">
        <div><h3 class="task-page-title">Team Oversight</h3><p class="task-page-subtitle">Monitor and manage your sales team's pipeline in real-time.</p></div>
    </div>

    <div class="rep-dropdown-wrapper" style="margin-bottom: 25px; max-width: 350px;">
        
        <label style="color: #9ca3af; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; font-weight: 600;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            Select Team Member
        </label>
        
        <select id="repSelectDropdown" class="custom-rep-select" onchange="if(this.value) selectRep(this.value)">
            <option value="" disabled selected>-- Choose a Representative --</option>
            
            @forelse($reps as $rep)
                <option value="{{ $rep->id }}">{{ $rep->name }}</option>
            @empty
                <option value="" disabled>No Sales Representatives found.</option>
            @endforelse
            
        </select>
    </div>

    <div id="repTaskContainer">
        <div class="text-center py-5" style="opacity: 0.5;">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 20px;">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            <h5>Select a Sales Rep above to view their board.</h5>
        </div>
    </div>
</div>

<div class="modal fade" id="convertClientModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Convert to Paying Client</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Complete the details below to finalize the sale.</p>
                    <input type="hidden" name="dental_office_id" id="convert_office_id">
                    <div class="form-group"><label>Office Name</label><input type="text" name="name" id="convert_office_name" class="form-control" readonly style="background-color: #e9ecef;"></div>
                    <div class="form-group" style="background: #f0f9ff; padding: 15px; border-radius: 8px; border: 1px dashed #bae6fd;"><label style="color: #0369a1; font-weight: 700;">Monthly Subscription Amount ($)</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text">$</span></div><input type="number" name="subscription_amount" class="form-control" placeholder="0.00" step="0.01" min="0" required></div><small style="color: #0284c7;">Enter the recurring monthly revenue for this client.</small></div>
                    <div class="form-group mt-3"><label>Contact Person <span class="text-danger">*</span></label><input type="text" name="contact_person" id="convert_contact_person" class="form-control" required placeholder="e.g. Dr. Smith"></div>
                    <div class="row"><div class="col-md-6"><div class="form-group"><label>Direct Email <span class="text-danger">*</span></label><input type="email" name="email" id="convert_email" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label>Direct Phone</label><input type="text" name="phone" id="convert_phone" class="form-control" placeholder="(555) 123-4567"></div></div></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Confirm Conversion</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewOfficeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title" id="view_office_title">Office Details</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
            <div class="modal-body" id="view_office_body"><div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div></div>
        </div>
    </div>
</div>

<div class="modal fade" id="taskCompletionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: #1e293b; color: white; border: 1px solid #334155;">
            <div class="modal-header" style="border-bottom: 1px solid #334155;"><h5 class="modal-title">Task Outcome</h5><button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body">
                <form id="completeTaskForm">
                    @csrf <input type="hidden" id="modal_task_id">
                    <div class="form-group">
                        <label>What happened?</label>
                        <select class="form-control" id="task_outcome_select" style="background: #0f172a; color: white; border: 1px solid #334155; margin-bottom: 10px;">
                            <option value="Email Sent - Waiting Response">Email Sent - Waiting Response</option>
                            <option value="Call Not Picked">Call Not Picked</option>
                            <option value="Left Voicemail">Left Voicemail</option>
                            <option value="Meeting Scheduled">Meeting Scheduled</option>
                            <option value="Not Interested">Not Interested</option>
                            <option value="Other">Other (Type below)</option>
                        </select>
                    </div>
                    {{-- NEW: Receptive Status --}}
                    <div class="form-group">
                        <label>Update Lead Status (Optional)</label>
                        <select class="form-control" id="task_receptive_status" style="background: #0f172a; color: white; border: 1px solid #334155; margin-bottom: 10px;">
                            <option value="">Keep Current Status</option>
                            <option value="HOT" style="color: #ef4444; font-weight: bold;">HOT</option>
                            <option value="WARM" style="color: #f59e0b; font-weight: bold;">WARM</option>
                            <option value="COLD" style="color: #3b82f6; font-weight: bold;">COLD</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Additional Notes (Optional)</label>
                        <textarea class="form-control" id="task_note" rows="3" placeholder="Enter specific details..." style="background: #0f172a; color: white; border: 1px solid #334155;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #334155;"><button type="button" class="btn btn-secondary" data-dismiss="modal" style="color:white; border-color: #334155;">Cancel</button><button type="button" class="btn btn-success" id="confirmTaskDone">Mark as Done</button></div>
        </div>
    </div>
</div>
<div id="customToast" style="display: none; position: fixed; top: 30px; right: 30px; z-index: 9999; padding: 15px 25px; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); font-weight: 600; color: white; display: flex; align-items: center; gap: 10px; opacity: 0; transition: opacity 0.3s ease;">
    <span id="toastIcon"></span>
    <span id="toastMessage"></span>
</div>

<script>
    var currentRepId = null;
    var autoRefreshInterval = null;
    var currentUrl = null; 
    let searchResetTimer; 

    // --- 1. SEARCH FUNCTION (Server-Side) ---
    $(document).on('keyup', '#taskSearchInput', function() {
        clearTimeout(searchResetTimer);
        searchResetTimer = setTimeout(function() {
            if(currentRepId) {
                // Reset to Page 1 when searching
                currentUrl = '/team-tasks/fetch/' + currentRepId;
                fetchTasks(false, true); 
            }
        }, 500);
    });

    // --- 2. SELECT REP ---
   // --- 2. SELECT REP ---
    function selectRep(repId) {
        currentRepId = repId;
        currentUrl = '/team-tasks/fetch/' + repId;
        
        // Sync the dropdown value (in case it's triggered from somewhere else)
        $('#repSelectDropdown').val(repId);

        // Show loading state
        $('#repTaskContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><div class="mt-2">Loading tasks...</div></div>');
        $('#taskSearchInput').val(''); // Clear Search
        
        fetchTasks(false, true);
        
        if (autoRefreshInterval) clearInterval(autoRefreshInterval);
        autoRefreshInterval = setInterval(function() { 
            var searchValue = $('#taskSearchInput').val();
            if(currentRepId && searchValue === '') { fetchTasks(true, false); }
        }, 5000); 
    }

    // --- 3. FETCH FUNCTION ---
    function fetchTasks(silent = false, force = false, customUrl = null) {
        if (!force && $('.modal.show').length > 0) { return; }
        
        var activeTabId = $('.nav-link-dark.active').attr('id');
        var ajaxUrl = customUrl || currentUrl;
        
        // Append Search Term
        var searchValue = $('#taskSearchInput').val();
        var separator = ajaxUrl.indexOf('?') === -1 ? '?' : '&';
        ajaxUrl += separator + 'search=' + encodeURIComponent(searchValue);
        ajaxUrl += '&t=' + new Date().getTime();
        
        $.get(ajaxUrl, function(html) {
            if(!silent) console.log("✅ Data Loaded");
            $('#repTaskContainer').html(html).css('opacity', '1');
            if(activeTabId) {
                $('.nav-link-dark').removeClass('active'); $('.tab-pane').removeClass('show active');
                $('#' + activeTabId).addClass('active');
                var targetPane = $('#' + activeTabId).attr('href'); $(targetPane).addClass('show active');
            }
        }).fail(function() { if(!silent) $('#repTaskContainer').html('<div class="text-center text-danger">Error loading tasks.</div>'); });
    }

    // --- 4. PAGINATION HANDLER ---
    $(document).on('click', '#repTaskContainer .pagination a', function(e) {
        e.preventDefault(); 
        var newUrl = $(this).attr('href'); 
        if(newUrl && newUrl !== '#' && currentRepId) {
            currentUrl = newUrl; 
            $('#repTaskContainer').css('opacity', '0.5');
            fetchTasks(false, true); 
        }
    });

    // --- 5. MARK AS DONE LOGIC (Updated for Receptive) ---
    $(document).ready(function() {
        $(document).on('click', '.mark-done-trigger', function(e) {
            e.preventDefault(); var taskId = $(this).data('id'); $('#modal_task_id').val(taskId); $('#taskCompletionModal').modal('show');
        });

        $('#confirmTaskDone').click(function() {
            var taskId = $('#modal_task_id').val(); var outcome = $('#task_outcome_select').val(); var receptive = $('#task_receptive_status').val(); var note = $('#task_note').val(); var finalNote = outcome + (note ? " - " + note : ""); 
            $(this).prop('disabled', true).text('Saving...');
            $.ajax({
                url: '/tasks/' + taskId + '/done', type: 'POST', 
                data: { _token: '{{ csrf_token() }}', completion_note: finalNote, receptive_status: receptive },
                success: function(response) { $('#taskCompletionModal').modal('hide'); if(currentRepId) fetchTasks(true, true); $('#confirmTaskDone').prop('disabled', false).text('Mark as Done'); $('#task_note').val(''); },
                error: function(err) { alert('Error updating task.'); $('#confirmTaskDone').prop('disabled', false).text('Mark as Done'); }
            });
        });
    });
    // Function for pop up
    function showToast(message, type = 'success') {
    var toast = $('#customToast');
    var icon = type === 'success' ? '✅' : '❌';
    var bg = type === 'success' ? '#10b981' : '#ef4444'; // Green for success, Red for error

    $('#toastIcon').text(icon);
    $('#toastMessage').text(message);
    
    toast.css({'background': bg, 'display': 'flex'}).animate({opacity: 1}, 200);

    // Hide after 3 seconds
    setTimeout(function() {
        toast.animate({opacity: 0}, 200, function() { $(this).hide(); });
    }, 3000);
    }

    // --- 6. CONVERT CLIENT HELPER (Pre-fill Data) ---
    function openConvertModal(el) { 
        var id = $(el).data('id');
        var name = $(el).data('name');
        var contact = $(el).data('contact');
        var email = $(el).data('email');
        var phone = $(el).data('phone');

        $('#convert_office_id').val(id);
        $('#convert_office_name').val(name);
        $('#convert_contact_person').val(contact ? contact : '');
        $('#convert_email').val(email ? email : '');
        $('#convert_phone').val(phone ? phone : '').trigger('input'); // Trigger format

        $('#convertClientModal').modal('show'); 
    }

    // --- 7. PHONE VALIDATION (Strict US) ---
    $(document).ready(function() {
        $('#convert_phone').on('input', function(e) {
            var input = $(this).val();
            var numbers = input.replace(/\D/g, '');
            if (numbers.length > 11) { numbers = numbers.substring(0, 11); }
            if (numbers.length > 0 && numbers.charAt(0) !== '1') { numbers = '1' + numbers; }
            var formatted = '';
            if (numbers.length > 0) { formatted += '+' + numbers.substring(0, 1); }
            if (numbers.length > 1) { formatted += ' (' + numbers.substring(1, 4); }
            if (numbers.length > 4) { formatted += ') ' + numbers.substring(4, 7); }
            if (numbers.length > 7) { formatted += '-' + numbers.substring(7, 11); }
            $(this).val(formatted);
        });
        
        // Modal Submit Handler
        $(document).on('submit', '#convertClientModal form', function(e) {
            e.preventDefault(); var form = $(this); var btn = form.find('button[type="submit"]'); btn.prop('disabled', true).text('Converting...');
            $.ajax({
                url: form.attr('action'), type: 'POST', data: form.serialize(),
                success: function(response) { $('#convertClientModal').modal('hide'); form[0].reset(); btn.prop('disabled', false).text('Confirm Conversion'); if(currentRepId) fetchTasks(true, true);
                    // NEW MODERN POPUP
                showToast('Office successfully converted to Client!', 'success');
                 },
                error: function(xhr) { btn.prop('disabled', false).text('Confirm Conversion');
                 showToast('Error converting to client. Try again.', 'error');
                 }
            });
        });
    });

    // --- 8. VIEW DETAILS AJAX ---
    function viewOfficeDetails(id, taskNote) {
        $('#viewOfficeModal').modal('show');
        $('#view_office_body').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div><div class="mt-2">Loading...</div></div>');
        $.ajax({
            url: '/dental_offices/' + id + '/edit', type: 'GET',
            success: function(response) {
                var o = response.office;
                var noteHtml = (taskNote && taskNote !== 'null' && taskNote.trim() !== '') ? `<div class="col-12 mt-3 p-3" style="background: #334155; border-radius: 8px; border-left: 4px solid #f59e0b;"><strong style="color: #fcd34d;">Last Note:</strong><br><span style="color: white;">${taskNote}</span></div>` : '';
                var html = `<div class="row"><div class="col-md-6 mb-3"><strong>Name:</strong><br>${o.name}</div><div class="col-md-6 mb-3"><strong>Phone:</strong><br>${o.phone || '-'}</div><div class="col-md-12"><strong>Status:</strong> ${o.receptive}</div>${noteHtml}</div>`;
                $('#view_office_title').text(o.name); $('#view_office_body').html(html);
            }
        });
    }
</script>
@endsection