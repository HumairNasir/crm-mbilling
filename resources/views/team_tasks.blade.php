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
</style>

<div class="content-main">
    
    <div class="task-page-header">
        <div>
            <h3 class="task-page-title">Team Oversight</h3>
            <p class="task-page-subtitle">Monitor and manage your sales team's pipeline in real-time.</p>
        </div>
    </div>

    <div class="rep-scroll-container">
        @forelse($reps as $rep)
            <div class="rep-btn" id="rep-btn-{{ $rep->id }}" onclick="selectRep({{ $rep->id }})">
                <div class="rep-avatar">{{ substr($rep->name, 0, 1) }}</div>
                {{ $rep->name }}
            </div>
        @empty
            <div class="alert alert-warning w-100">No Sales Representatives found in your territory.</div>
        @endforelse
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
                    <div class="form-group mt-3"><label>Contact Person <span class="text-danger">*</span></label><input type="text" name="contact_person" class="form-control" required placeholder="e.g. Dr. Smith"></div>
                    <div class="row"><div class="col-md-6"><div class="form-group"><label>Direct Email <span class="text-danger">*</span></label><input type="email" name="email" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label>Direct Phone</label><input type="text" name="phone" class="form-control"></div></div></div>
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
                        <label>Additional Notes (Optional)</label>
                        <textarea class="form-control" id="task_note" rows="3" placeholder="Enter specific details..." style="background: #0f172a; color: white; border: 1px solid #334155;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #334155;"><button type="button" class="btn btn-secondary" data-dismiss="modal" style="color:white; border-color: #334155;">Cancel</button><button type="button" class="btn btn-success" id="confirmTaskDone">Mark as Done</button></div>
        </div>
    </div>
</div>

<script>
    var currentRepId = null;
    var autoRefreshInterval = null;
    var currentUrl = null; // <--- NEW: Stores the current page URL
    let searchResetTimer; 

    // --- 1. SEARCH WITH 10 SEC TIMER ---
    $(document).on('keyup', '#taskSearchInput', function() {
        var filter = $(this).val().toUpperCase();
        clearTimeout(searchResetTimer);

        // Determine which table is currently visible
        var tableId = $('.nav-link-dark.active').attr('href') === '#activeTasks' ? '#activeTasksTable' : '#pastTasksTable';
        
        $(tableId + " tbody tr").each(function() {
            var row = $(this);
            var tdName = row.find('td').eq(0).text().toUpperCase(); 
            var tdArea = row.find('td').eq(1).text().toUpperCase(); 
            var tdNote = (tableId === '#pastTasksTable') ? row.find('td').eq(3).text().toUpperCase() : "";

            if(tdName.indexOf(filter) > -1 || tdArea.indexOf(filter) > -1 || tdNote.indexOf(filter) > -1) {
                row.show();
            } else {
                row.hide();
            }
        });

        searchResetTimer = setTimeout(function() {
            $('#taskSearchInput').val(''); 
            $('#activeTasksTable tbody tr, #pastTasksTable tbody tr').show(); 
        }, 10000);
    });

    // --- 2. SELECT REP ---
    function selectRep(repId) {
        currentRepId = repId;
        
        // Reset URL to Page 1 (Base URL) when selecting a new rep
        currentUrl = '/team-tasks/fetch/' + repId;

        $('.rep-btn').removeClass('active');
        $('#rep-btn-' + repId).addClass('active');
        $('#repTaskContainer').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><div class="mt-2">Loading tasks...</div></div>');
        
        // Initial Fetch
        fetchTasks(false, true);

        // Start Auto-Refresh
        if (autoRefreshInterval) clearInterval(autoRefreshInterval);
        autoRefreshInterval = setInterval(function() { 
            if(currentRepId) fetchTasks(true, false); // Silent Refresh
        }, 5000); 
    }

    // --- 3. FETCH FUNCTION (FIXED) ---
    // Now uses the global 'currentUrl' variable so it remembers the page
    function fetchTasks(silent = false, force = false) {
        if (!force && $('.modal.show').length > 0) { return; }
        
        var activeTabId = $('.nav-link-dark.active').attr('id');
        
        // Add timestamp to the CURRENT URL (Page 1 or Page 2, etc)
        // We use 'currentUrl' which is updated by pagination clicks
        var ajaxUrl = currentUrl;
        ajaxUrl += (ajaxUrl.indexOf('?') === -1 ? '?' : '&') + 't=' + new Date().getTime();
        
        $.get(ajaxUrl, function(html) {
            if(!silent) console.log("✅ Data Loaded: " + currentUrl);
            
            $('#repTaskContainer').html(html).css('opacity', '1');
            
            if(activeTabId) {
                $('.nav-link-dark').removeClass('active'); 
                $('.tab-pane').removeClass('show active');
                $('#' + activeTabId).addClass('active');
                var targetPane = $('#' + activeTabId).attr('href'); 
                $(targetPane).addClass('show active');
            }
        }).fail(function() { 
            if(!silent) $('#repTaskContainer').html('<div class="text-center text-danger">Error loading tasks.</div>'); 
        });
    }

    // ====================================================================
    // ✅ PAGINATION HANDLER (Updates global 'currentUrl')
    // ====================================================================
    $(document).on('click', '#repTaskContainer .pagination a, #repTaskContainer .page-link, #repTaskContainer nav[role="navigation"] a', function(e) {
        e.preventDefault(); 
        
        var newUrl = $(this).attr('href'); 
        
        if(newUrl && newUrl !== '#' && currentRepId) {
            console.log("Pagination Clicked: " + newUrl);
            
            // UPDATE THE GLOBAL VARIABLE
            currentUrl = newUrl; 

            $('#repTaskContainer').css('opacity', '0.5');
            
            // Fetch immediately
            fetchTasks(false, true); 
        }
    });

    // --- 5. MARK AS DONE LOGIC ---
    $(document).ready(function() {
        $(document).on('click', '.mark-done-trigger', function(e) {
            e.preventDefault(); var taskId = $(this).data('id'); $('#modal_task_id').val(taskId); $('#taskCompletionModal').modal('show');
        });

        $('#confirmTaskDone').click(function() {
            var taskId = $('#modal_task_id').val(); var finalNote = $('#task_outcome_select').val() + ($('#task_note').val() ? " - " + $('#task_note').val() : ""); 
            $(this).prop('disabled', true).text('Saving...');
            $.ajax({
                url: '/tasks/' + taskId + '/done', type: 'POST', data: { _token: '{{ csrf_token() }}', completion_note: finalNote },
                success: function(response) { $('#taskCompletionModal').modal('hide'); if(currentRepId) fetchTasks(true, true); $('#confirmTaskDone').prop('disabled', false).text('Mark as Done'); $('#task_note').val(''); },
                error: function(err) { alert('Error updating task.'); $('#confirmTaskDone').prop('disabled', false).text('Mark as Done'); }
            });
        });
    });

    // --- 6. CONVERT & VIEW HELPERS ---
    $(document).on('submit', '#convertClientModal form', function(e) {
        e.preventDefault(); var form = $(this); var btn = form.find('button[type="submit"]'); btn.prop('disabled', true).text('Converting...');
        $.ajax({
            url: form.attr('action'), type: 'POST', data: form.serialize(),
            success: function(response) { $('#convertClientModal').modal('hide'); form[0].reset(); btn.prop('disabled', false).text('Confirm Conversion'); if(currentRepId) fetchTasks(true, true); alert('Success!'); },
            error: function(xhr) { btn.prop('disabled', false).text('Confirm Conversion'); alert('Error converting.'); }
        });
    });

    function openConvertModal(name, id) { $('#convert_office_name').val(name); $('#convert_office_id').val(id); $('#convertClientModal').modal('show'); }

    function viewOfficeDetails(id, taskNote) {
        $('#viewOfficeModal').modal('show');
        $('#view_office_body').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
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