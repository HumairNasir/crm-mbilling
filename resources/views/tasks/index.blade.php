@extends('layouts.backend')

@section('content')
<style>
    /* --- PAGE LAYOUT --- */
    .task-page-header { margin-bottom: 25px; display: flex; justify-content: space-between; align-items: end; }
    .task-page-title { font-size: 24px; font-weight: 700; color: #2c3e50; margin: 0; }
    .task-page-subtitle { font-size: 14px; color: #6c757d; margin-bottom: 0; }

    /* --- PREMIUM DARK THEME --- */
    .dashboard-card-dark {
        background: linear-gradient(145deg, #1e293b, #0f172a);
        border-radius: 16px; padding: 20px; position: relative; overflow: hidden;
        color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.05); min-height: 500px;
    }
    .glow-effect { position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: radial-gradient(circle, rgba(56,189,248,0.2) 0%, rgba(0,0,0,0) 70%); z-index: 0; }
    .relative-z { position: relative; z-index: 1; }

    /* TABS STYLING */
    .nav-pills-dark { display: flex; gap: 10px; margin-bottom: 20px; }
    .nav-link-dark {
        background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.6);
        border: 1px solid rgba(255,255,255,0.1); border-radius: 30px;
        padding: 8px 20px; font-size: 13px; font-weight: 600; transition: all 0.3s;
    }
    .nav-link-dark:hover { background: rgba(255,255,255,0.1); color: white; }
    .nav-link-dark.active {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white; border-color: transparent; box-shadow: 0 4px 12px rgba(59,130,246,0.4);
    }
    .badge-count { background: white; color: #2563eb; padding: 2px 6px; border-radius: 10px; font-size: 10px; font-weight: 800; margin-left: 8px; }

    /* SEARCH & TABLE */
    .search-dark-wrapper { position: relative; width: 300px; margin-left: auto; }
    .search-dark-input { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: white; padding: 10px 15px 10px 35px; font-size: 13px; width: 100%; transition: all 0.2s; }
    .search-dark-input:focus { background: rgba(255,255,255,0.1); outline: none; border-color: rgba(255,255,255,0.3); }
    .search-icon-dark { position: absolute; left: 12px; top: 12px; width: 14px; opacity: 0.5; color: white; }

    .table-dark-custom { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
    .table-dark-custom thead th { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); padding: 0 15px 10px 15px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .table-dark-custom tbody tr { background: rgba(255,255,255,0.02); transition: background 0.2s; }
    .table-dark-custom tbody tr:hover { background: rgba(255,255,255,0.05); }
    .table-dark-custom td { padding: 15px; border: none; color: rgba(255,255,255,0.9); font-size: 13px; vertical-align: middle; }
    .table-dark-custom td:first-child { border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .table-dark-custom td:last-child { border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

    /* BUTTONS */
    .btn-icon-dark { background: rgba(255,255,255,0.1); border: none; border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; color: white; transition: all 0.2s; cursor: pointer; }
    .btn-icon-dark:hover { background: rgba(255,255,255,0.2); transform: scale(1.05); }
    
    .btn-convert-small { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; border: none; border-radius: 6px; padding: 6px 14px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; cursor: pointer; transition: all 0.2s; }
    .btn-convert-small:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(34,197,94,0.3); }

    .btn-mark-done { color: rgba(255,255,255,0.5); font-size: 12px; background: none; border: none; text-decoration: underline; cursor: pointer; transition: color 0.2s; }
    .btn-mark-done:hover { color: #4ade80; }

    .sub-text { font-size: 11px; color: rgba(255,255,255,0.4); display: block; margin-top: 4px; }
    .ai-badge { background: rgba(59,130,246,0.2); color: #93c5fd; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; margin-right: 5px; }
    
    /* PAGINATION FIX */
    .pagination { justify-content: center; margin-top: 20px; }
    .page-item .page-link { background-color: #1e293b; border-color: #334155; color: #cbd5e1; font-size: 12px; padding: 8px 12px; }
    .page-item.active .page-link { background-color: #3b82f6; border-color: #3b82f6; color: white; font-weight: 700; }
    .page-item.disabled .page-link { background-color: #0f172a; border-color: #334155; color: #475569; }
    .page-link:hover { background-color: #334155; color: white; border-color: #475569; }
</style>

{{-- DETECT WHICH TAB SHOULD BE ACTIVE --}}
@php 
    $showPast = request()->get('tab') === 'active' || request()->has('past_page'); 
@endphp


<div class="content-main">
    
    <div class="task-page-header">
        <div>
            <h3 class="task-page-title">Task Management</h3>
            <p class="task-page-subtitle">Track, follow up, and convert your leads.</p>
        </div>
    </div>

    <div class="dashboard-card-dark">
        <div class="glow-effect"></div>
        <div class="relative-z">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="nav nav-pills-dark" id="taskTabs" role="tablist">
                    <a class="nav-link-dark {{ !$showPast ? 'active' : '' }}" id="active-tab" data-toggle="tab" href="#activeTasks" role="tab">
                        Active Tasks @if($active_tasks->count() > 0) <span class="badge-count">{{ $active_tasks->count() }}</span> @endif
                    </a>
                    <a class="nav-link-dark {{ $showPast ? 'active' : '' }}" id="past-tab" data-toggle="tab" href="#pastTasks" role="tab">
                        Past History
                    </a>
                </div>

                <!-- <div class="search-dark-wrapper">
                    <svg class="search-icon-dark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <input type="text" id="taskSearchInput" class="search-dark-input" placeholder="Search Office, Area, or Notes...">
                </div> -->
                 
                <form method="GET" action="{{ route('tasks.index') }}">
                    <input type="hidden" name="tab" value="{{ $showPast ? 'past' : 'active' }}">

                    <div class="search-dark-wrapper">
                        <svg class="search-icon-dark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>

                        <input 
                            type="text" 
                            name="search"
                            value="{{ request('search') }}"
                            class="search-dark-input" 
                            placeholder="Search Office, Area, or Notes..."
                        >
                    </div>
                </form>




            </div>

            <div class="tab-content" id="taskTabsContent">
                
                {{-- ACTIVE TASKS TAB --}}
                <div class="tab-pane fade {{ !$showPast ? 'show active' : '' }}" id="activeTasks" role="tabpanel">
                    <div style="max-height: 600px; overflow-y: auto;">
                        <table class="table-dark-custom" id="activeTasksTable">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">Dental Office</th>
                                    <th style="width: 20%;">Area / Region</th>
                                    <th style="width: 30%;">AI Suggested Strategy</th>
                                    <th style="width: 20%; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($active_tasks as $task)
                                <tr>
                                    <td>
                                        <strong style="font-size: 14px;">{{ $task->dentalOffice->name ?? 'Unknown Office' }}</strong>
                                        <span class="sub-text">
                                            <i class="fa fa-user" style="opacity:0.5; margin-right:3px;"></i> {{ $task->dentalOffice->contact_person ?? 'No Contact' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $task->dentalOffice->state->name ?? '-' }}
                                        <span class="sub-text">{{ $task->dentalOffice->region->name ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span class="ai-badge">AI Strategy</span>
                                        <span style="font-size: 12px; color: rgba(255,255,255,0.8);">
                                            {{ Str::limit($task->ai_suggested_approach ?? 'Analyze office size and pitch the premium retention package.', 80) }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="d-flex align-items-center justify-content-end gap-2">
                                            {{-- VIEW BUTTON --}}
                                            <button class="btn-icon-dark" onclick="viewOfficeDetails({{ $task->dentalOffice->id }}, '{{ addslashes($task->completion_note ?? '') }}')" title="View Details">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                            </button>
                                            
                                            {{-- DONE BUTTON --}}
                                            <button type="button" class="btn-mark-done mx-2 mark-done-trigger" data-id="{{ $task->id }}">
                                                Done
                                            </button>

                                            {{-- CONVERT BUTTON (Passing Data) --}}
                                            <button class="btn-convert-small" 
                                                onclick="openConvertModal(this)" 
                                                data-id="{{ $task->dentalOffice->id }}"
                                                data-name="{{ addslashes($task->dentalOffice->name) }}"
                                                data-contact="{{ addslashes($task->dentalOffice->contact_person ?? '') }}"
                                                data-email="{{ addslashes($task->dentalOffice->email ?? '') }}"
                                                data-phone="{{ addslashes($task->dentalOffice->phone ?? '') }}">
                                                Convert
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center" style="padding: 40px; opacity: 0.5;">No active tasks found. Great job!</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $active_tasks->appends(['past_page' => request('past_page')])->links() }}
                    </div>
                </div>

                {{-- PAST HISTORY TAB --}}
                <div class="tab-pane fade {{ $showPast ? 'show active' : '' }}" id="pastTasks" role="tabpanel">
                    <div style="max-height: 600px; overflow-y: auto;">
                        <table class="table-dark-custom" id="pastTasksTable">
                            <thead>
                                <tr>
                                    <th style="width: 25%;">Dental Office</th>
                                    <th style="width: 15%;">Area</th>
                                    <th style="width: 15%;">Status</th>
                                    <th style="width: 25%;">Completion Note</th>
                                    <th style="width: 20%; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($past_tasks as $task)
                                <tr>
                                    <td>
                                        <strong>{{ $task->dentalOffice->name ?? '-' }}</strong>
                                        <span class="sub-text">{{ \Carbon\Carbon::parse($task->completed_at)->format('M d, Y h:i A') }}</span>
                                    </td>
                                    <td>{{ $task->dentalOffice->state->name ?? '-' }}</td>
                                    <td>
                                        @if($task->status == 'converted')
                                            <span style="color: #4ade80; font-weight: 700; font-size: 11px; border: 1px solid #4ade80; padding: 2px 8px; border-radius: 10px;">CONVERTED</span>
                                        @else
                                            <span style="color: #94a3b8; font-weight: 600; font-size: 11px;">COMPLETED</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($task->completion_note)
                                            <span style="color: #cbd5e1; font-size: 12px; font-style: italic;">"{{ Str::limit($task->completion_note, 40) }}"</span>
                                        @else
                                            <span style="color: rgba(255,255,255,0.2); font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        {{-- View Details --}}
                                        <button class="btn-icon-dark mr-1" onclick="viewOfficeDetails({{ $task->dentalOffice->id }}, '{{ addslashes($task->completion_note ?? '') }}')" title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>

                                        {{-- Revive Button (Passing Data) --}}
                                        @if($task->status == 'completed')
                                            <button class="btn-convert-small" style="background: linear-gradient(135deg, #6366f1, #4f46e5);" 
                                                onclick="openConvertModal(this)" 
                                                data-id="{{ $task->dentalOffice->id }}"
                                                data-name="{{ addslashes($task->dentalOffice->name) }}"
                                                data-contact="{{ addslashes($task->dentalOffice->contact_person ?? '') }}"
                                                data-email="{{ addslashes($task->dentalOffice->email ?? '') }}"
                                                data-phone="{{ addslashes($task->dentalOffice->phone ?? '') }}"
                                                title="Revive Lead">
                                                Revive
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center" style="padding: 40px; opacity: 0.5;">No history available.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $past_tasks->appends(['active_page' => request('active_page')])->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="convertClientModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Convert to Paying Client</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Complete the details below to finalize the sale.</p>
                    
                    <input type="hidden" name="dental_office_id" id="convert_office_id">

                    <div class="form-group">
                        <label>Office Name</label>
                        <input type="text" name="name" id="convert_office_name" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>

                    <div class="form-group" style="background: #f0f9ff; padding: 15px; border-radius: 8px; border: 1px dashed #bae6fd;">
                        <label style="color: #0369a1; font-weight: 700;">Monthly Subscription Amount ($)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" name="subscription_amount" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                        </div>
                        <small style="color: #0284c7;">Enter the recurring monthly revenue for this client.</small>
                    </div>

                    <div class="form-group mt-3">
                        <label>Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="contact_person" id="convert_contact_person" class="form-control" required placeholder="e.g. Dr. Smith">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Direct Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="convert_email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Direct Phone</label>
                                <input type="text" name="phone" id="convert_phone" class="form-control" placeholder="(555) 555-5555">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Confirm Conversion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewOfficeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="view_office_title">Office Details</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="view_office_body">
                <div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="taskCompletionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="background: #1e293b; color: white; border: 1px solid #334155;">
            <div class="modal-header" style="border-bottom: 1px solid #334155;">
                <h5 class="modal-title">Task Outcome</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="completeTaskForm">
                    @csrf
                    <input type="hidden" id="modal_task_id">
                    
                    {{-- Result Select --}}
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

                    {{-- Receptive Status Dropdown --}}
                    <div class="form-group">
                        <label>Update Lead Status (Optional)</label>
                        <select class="form-control" id="task_receptive_status" style="background: #0f172a; color: white; border: 1px solid #334155; margin-bottom: 10px;">
                            <option value="">Keep Current Status</option>
                            <option value="HOT" style="color: #ef4444; font-weight: bold;">HOT</option>
                            <option value="WARM" style="color: #f59e0b; font-weight: bold;">WARM</option>
                            <option value="COLD" style="color: #3b82f6; font-weight: bold;">COLD</option>
                        </select>
                    </div>

                    {{-- Note Area --}}
                    <div class="form-group">
                        <label>Additional Notes (Optional)</label>
                        <textarea class="form-control" id="task_note" rows="3" placeholder="Enter specific details..." style="background: #0f172a; color: white; border: 1px solid #334155;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #334155;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="color:white; border-color: #334155;">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmTaskDone">Mark as Done</button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. SEARCH FUNCTION WITH AUTO-RESET TIMER ---
    let searchResetTimer; 

    // document.getElementById('taskSearchInput').addEventListener('keyup', function() {
    //     var filter = this.value.toUpperCase();
        
    //     clearTimeout(searchResetTimer);
    //     filterTable(filter);

    //     searchResetTimer = setTimeout(function() {
    //         var input = document.getElementById('taskSearchInput');
    //         input.value = ''; 
    //         filterTable(''); 
    //         console.log("Search auto-reset due to inactivity.");
    //     }, 10000); 
    // });

    // function filterTable(filter) {
    //     var activeTab = document.querySelector('.nav-link-dark.active').getAttribute('href');
    //     var tableId = (activeTab === '#activeTasks') ? 'activeTasksTable' : 'pastTasksTable';
        
    //     var table = document.getElementById(tableId);
    //     var tr = table.getElementsByTagName("tr");

    //     for (var i = 1; i < tr.length; i++) {
    //         var tdName = tr[i].getElementsByTagName("td")[0]; 
    //         var tdArea = tr[i].getElementsByTagName("td")[1]; 
    //         var tdNote = (tableId === 'pastTasksTable') ? tr[i].getElementsByTagName("td")[3] : null; 
            
    //         if (tdName && tdArea) {
    //             var txtName = tdName.textContent || tdName.innerText;
    //             var txtArea = tdArea.textContent || tdArea.innerText;
    //             var txtNote = tdNote ? (tdNote.textContent || tdNote.innerText) : "";
                
    //             if (txtName.toUpperCase().indexOf(filter) > -1 || txtArea.toUpperCase().indexOf(filter) > -1 || txtNote.toUpperCase().indexOf(filter) > -1) {
    //                 tr[i].style.display = "";
    //             } else {
    //                 tr[i].style.display = "none";
    //             }
    //         }       
    //     }
    // }

    // --- 2. MARK AS DONE LOGIC ---
    $(document).ready(function() {
        $(document).on('click', '.mark-done-trigger', function(e) {
            e.preventDefault(); 
            var taskId = $(this).data('id'); 
            $('#modal_task_id').val(taskId); 
            $('#taskCompletionModal').modal('show');
        });

        $('#confirmTaskDone').click(function() {
            var taskId = $('#modal_task_id').val(); 
            var outcome = $('#task_outcome_select').val(); 
            var receptive = $('#task_receptive_status').val(); 
            var note = $('#task_note').val(); 
            var finalNote = outcome + (note ? " - " + note : ""); 

            $(this).prop('disabled', true).text('Saving...');

            $.ajax({
                url: '/tasks/' + taskId + '/done', 
                type: 'POST', 
                data: { 
                    _token: '{{ csrf_token() }}', 
                    completion_note: finalNote,
                    receptive_status: receptive 
                },
                success: function(response) { 
                    $('#taskCompletionModal').modal('hide'); 
                    location.reload(); 
                },
                error: function(err) { 
                    alert('Error updating task. Please try again.'); 
                    $('#confirmTaskDone').prop('disabled', false).text('Mark as Done'); 
                }
            });
        });
    });

    // --- 3. CONVERT CLIENT HELPER (With Data Pre-fill) ---
    function openConvertModal(el) { 
        var id = $(el).data('id');
        var name = $(el).data('name');
        var contact = $(el).data('contact');
        var email = $(el).data('email');
        var phone = $(el).data('phone');

        $('#convert_office_id').val(id);
        $('#convert_office_name').val(name);
        
        // Use existing data or empty string
        $('#convert_contact_person').val(contact ? contact : '');
        $('#convert_email').val(email ? email : '');
        
        // Pre-fill phone if available (it will just be plain text initially)
        $('#convert_phone').val(phone ? phone : '');
        // Trigger input event to format existing phone number immediately
        $('#convert_phone').trigger('input');

        $('#convertClientModal').modal('show'); 
    }

    // --- 4. STRICT US PHONE VALIDATION (+1 XXX-XXX-XXXX) ---
    $(document).ready(function() {
        $('#convert_phone').on('input', function(e) {
            var input = $(this).val();
            
            // 1. Strip everything that is NOT a number
            var numbers = input.replace(/\D/g, '');

            // 2. Strict Length Limit: 11 Digits Max (1 Country Code + 10 Phone)
            // If user types more, we truncate it.
            if (numbers.length > 11) {
                numbers = numbers.substring(0, 11);
            }

            // 3. Enforce US Country Code '1'
            // If the first digit isn't 1, or if it's empty, we treat it as if they are starting to type the area code.
            // But to enforce standard, we assume the first digit MUST be 1. 
            // If they typed '1', great. If they typed '2' (area code start), we prepend '1'.
            if (numbers.length > 0 && numbers.charAt(0) !== '1') {
                numbers = '1' + numbers;
            }

            // 4. Formatting Logic
            var formatted = '';
            if (numbers.length > 0) {
                // Country Code
                formatted += '+' + numbers.substring(0, 1);
            }
            if (numbers.length > 1) {
                // Area Code: (XXX)
                formatted += ' (' + numbers.substring(1, 4);
            }
            if (numbers.length > 4) {
                // Prefix: XXX-
                formatted += ') ' + numbers.substring(4, 7);
            }
            if (numbers.length > 7) {
                // Line Number: XXXX
                formatted += '-' + numbers.substring(7, 11);
            }

            // 5. Update Value
            $(this).val(formatted);
        });
    });

    // --- 5. VIEW DETAILS AJAX ---
    function viewOfficeDetails(id, taskNote) {
        $('#viewOfficeModal').modal('show');
        $('#view_office_body').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div><div class="mt-2">Loading...</div></div>');

        $.ajax({
            url: '/dental_offices/' + id + '/edit', 
            type: 'GET',
            success: function(response) {
                var o = response.office;
                var regionName = (o.region) ? o.region.name : '<em>N/A</em>';
                var stateName = (o.state) ? o.state.name : '<em>N/A</em>';
                var repName = (o.sales_rep) ? o.sales_rep.name : '<em>Unassigned</em>';

                var noteHtml = '';
                if(taskNote && taskNote !== 'null' && taskNote.trim() !== '') {
                    noteHtml = `
                        <div class="col-12 mt-3 p-3" style="background: #334155; border-radius: 8px; border-left: 4px solid #f59e0b;">
                            <strong style="color: #fcd34d;">Last Interaction Outcome:</strong><br>
                            <span style="color: white;">${taskNote}</span>
                        </div>
                    `;
                }

                var html = `
                    <div class="row">
                        <div class="col-md-6 mb-3"><strong>Name:</strong><br>${o.name}</div>
                        <div class="col-md-6 mb-3"><strong>Doctor:</strong><br>${o.dr_name || '-'}</div>
                        <div class="col-md-6 mb-3"><strong>Phone:</strong><br>${o.phone || '-'}</div>
                        <div class="col-md-6 mb-3"><strong>Email:</strong><br>${o.email || '-'}</div>
                        <div class="col-md-12 mb-3"><strong>Address:</strong><br>${o.address || '-'}</div>
                        
                        <div class="col-12"><hr style="border-top: 1px solid #475569;"></div>

                        <div class="col-md-4 mb-3"><strong>Region:</strong><br>${regionName}</div>
                        <div class="col-md-4 mb-3"><strong>Area:</strong><br>${stateName}</div>
                        <div class="col-md-4 mb-3"><strong>Sales Rep:</strong><br>${repName}</div>

                        <div class="col-md-12">
                            <strong>Receptive Status:</strong> 
                            <span class="badge badge-info">${o.receptive || 'Cold'}</span>
                        </div>
                        ${noteHtml}
                    </div>
                `;
                $('#view_office_title').text(o.name); 
                $('#view_office_body').html(html);
            },
            error: function(xhr, status, error) {
                $('#view_office_body').html('<div class="text-center text-danger p-3">Error loading details</div>');
            }
        });
    }
</script>
@endsection