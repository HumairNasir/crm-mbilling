<div class="dashboard-card-dark">
    <div class="glow-effect"></div>
    <div class="relative-z">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="nav nav-pills-dark" id="taskTabs" role="tablist">
                <a class="nav-link-dark active" id="active-tab" data-toggle="tab" href="#activeTasks" role="tab">
                    Active Tasks 
                    @if($active_tasks->count() > 0) <span class="badge-count">{{ $active_tasks->count() }}</span> @endif
                </a>
                <a class="nav-link-dark" id="past-tab" data-toggle="tab" href="#pastTasks" role="tab">
                    Past History
                </a>
            </div>

            <div class="search-dark-wrapper">
                <svg class="search-icon-dark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <input type="text" id="taskSearchInput" class="search-dark-input" placeholder="Search Office, Area, or Notes...">
            </div>
        </div>

        <div class="tab-content" id="taskTabsContent">
            
            <div class="tab-pane fade show active" id="activeTasks" role="tabpanel">
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
                                        <button class="btn-icon-dark" onclick="viewOfficeDetails({{ $task->dentalOffice->id }}, '')" title="View Details">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                        
                                        <button type="button" class="btn-mark-done mx-2 mark-done-trigger" data-id="{{ $task->id }}">
                                            Done
                                        </button>

                                        {{-- CONVERT BUTTON (Updated to pass data) --}}
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
                            <tr><td colspan="4" class="text-center" style="padding: 40px; opacity: 0.5;">No active tasks found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $active_tasks->appends(['past_page' => request('past_page')])->links() }}
                </div>
            </div>

            <div class="tab-pane fade" id="pastTasks" role="tabpanel">
                <div style="max-height: 600px; overflow-y: auto;">
                    <table class="table-dark-custom" id="pastTasksTable">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Dental Office</th>
                                <th style="width: 15%;">Area</th>
                                <th style="width: 15%;">Status</th>
                                <th style="width: 25%;">Completion Note</th> <th style="width: 20%; text-align: right;">Action</th>
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
                                    {{-- NOTE DATA --}}
                                    @if($task->completion_note)
                                        <span style="color: #cbd5e1; font-size: 12px; font-style: italic;">"{{ Str::limit($task->completion_note, 40) }}"</span>
                                    @else
                                        <span style="color: rgba(255,255,255,0.2); font-size: 12px;">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    {{-- View Details (Pass Note) --}}
                                    <button class="btn-icon-dark mr-1" onclick="viewOfficeDetails({{ $task->dentalOffice->id }}, '{{ addslashes($task->completion_note ?? '') }}')" title="View Details">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    </button>

                                    {{-- Revive Button (Pass Data) --}}
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