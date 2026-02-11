@extends('layouts.backend')
@section('content')
<style>
    /* MODAL SCROLLBAR & SIZE FIX */
    .form-office-modal-dialog {
        max-width: 600px; /* Adjust width as needed */
    }
    .modal-body {
        max-height: 70vh; /* 70% of viewport height */
        overflow-y: auto; /* Scrollbar if content exceeds height */
    }

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
        max-height: 250px; /* Internal dropdown scroll */
        overflow-y: auto;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .checkbox-dropdown-list.show {
        display: block;
    }

    .checkbox-dropdown-item {
        display: block;
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
        margin-bottom: 0;
    }

    .checkbox-dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .checkbox-dropdown-item.is-selected {
        background-color: #d4edda;
        color: #155724;
    }

    /* HEADER STYLE FOR REGION GROUPS */
    .dropdown-header {
        background-color: #e9ecef;
        font-weight: bold;
        font-size: 12px;
        padding: 5px 10px;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .region-info-badges {
        margin-top: 5px;
        margin-bottom: 10px;
        font-size: 12px;
    }
    .region-badge {
        background: #e0e0e0;
        color: #333;
        padding: 2px 6px;
        border-radius: 4px;
        margin-right: 4px;
        display: inline-block;
        margin-bottom: 3px;
    }
</style>

<div class="content-main dental-office-content">
    <h3>Area Managers</h3>
    <div class="">
        <div class="dental-office-parent">
            <div class="search-main search-employee">
                <input type="search" name="search" id="search" placeholder="Search name, email, state..." class="dental-office-search">
                <img src="../images/search.svg" alt="">
            </div>
            <div class="category-search search-employee dental-category">
                <select name="cats" id="categories">
                    <option value="" disabled selected>Sorted By</option>
                    <option value="dummy1">All</option>
                </select>
            </div>
            <div class="add-dental-button">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">Add Area Manager</button>
            </div>
        </div>
        
        <div class="dental-table-main">
            <table class="dental-office-table">
                <thead class="dental-table-header">
                    <tr>
                        <td class="dp-none">#</td>
                        <td>Name</td>
                        <td>Email</td>
                        <td class="dp-none">Phone</td>
                        <td class="dp-none">Assigned States</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($area_managers as $manager)
                    <tr>
                        <td class="dp-none">{{ $loop->index + 1 }}</td>
                        <td>
                            <div class="dental-office-name-img">
                                <img src="../images/img.svg" alt="">
                                <h6>{{ $manager->name }}</h6>
                            </div>
                        </td>
                        <td class="email-button"><a href="#"><button><img src="../images/email.svg" alt="">{{ $manager->email }}</button></a></td>
                        <td class="dp-none"><span>{{ $manager->phone ?? '-' }}</span></td>
                        <td class="dp-none">
                            @if($manager->states->count() > 0)
                                @foreach($manager->states as $state)
                                <span class="badge badge-info" style="background-color: #007bff; color: white; padding: 5px 8px; border-radius: 4px; font-size: 11px; margin-right: 3px;">
                                    {{ $state->name }}
                                </span>
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td class="action-icons">
                            <div class='d-flex align-items-center'>
                                <form action="" method="POST" id="edit-form-{{ $manager->id }}" class="edit-form m-0">
                                    @csrf @method('Update')
                                    <button class="btn" type="button">
                                        <img style="background: #e0e0e0; padding: 5px; border-radius: 4px;" src="../images/pencil.svg" alt="" title="Edit" data-id="{{ $manager->id }}" class="edit-area-manager-btn">
                                    </button>
                                </form>
                                <form action="{{ route('area_manager.destroy', $manager->id) }}" method="POST" id="delete-form-{{ $manager->id }}" class="delete-form m-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn delete-btn" data-id="{{ $manager->id }}">
                                        <img src="../images/trash.svg" alt="" style="background: #e0e0e0; padding: 5px; border-radius: 4px;">
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Area Manager</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="add-area-manager">
                    @csrf
                    <div class="form-office-main">
                        <div>
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" placeholder="John">
                            <span class="name-error text-danger"></span>

                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" placeholder="abc@example.com">
                            <span class="email-error text-danger"></span>

                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" placeholder="*******">
                            <span class="password-error text-danger"></span>

                            <label for="assign">Assign to Regional Manager <span class="text-danger">*</span></label>
                            <select name="assign" id="assign" class="form-control regional-manager-select">
                                <option value="">Select Regional Manager</option>
                                @foreach($regional_managers as $regional)
                                <option value="{{ $regional->id }}">{{ $regional->name }}</option>
                                @endforeach
                            </select>
                            
                            <div class="region-info-badges" id="add-region-badges"></div>
                            
                            <span class="assign-error text-danger"></span>
                        </div>
                        <div>
                            <label for="phone">Phone</label>
                            <input type="number" name="phone" id="phone" placeholder="+123 456 789">
                            <span class="phone-error text-danger"></span>

                            <label for="address">Address</label>
                            <input type="text" name="address" id="address" placeholder="Address">
                            <span class="address-error text-danger"></span>

                            <div class="form-group">
                                <label>Assign States <span class="text-danger">*</span></label>
                                
                                <div class="checkbox-dropdown" id="area-dropdown-container">
                                    <div class="checkbox-dropdown-text">Select Manager First</div>
                                    <div class="checkbox-dropdown-list" id="area-checkbox-list"></div>
                                </div>
                                <select name="areas[]" id="real-area-select" multiple style="display:none;"></select>
                                <span class="areas-error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">Add Area Manager</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-area-manager" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Area Manager</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="update-area-manager">
                    @csrf
                    <div class="form-office-main">
                        <div>
                            <label>Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edt-name">
                            <span class="name-error text-danger"></span>

                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="edt-email">
                            <span class="email-error text-danger"></span>

                            <label>Password</label>
                            <input type="password" name="password" id="edt-password" placeholder="Leave blank to keep">
                            <span class="password-error text-danger"></span>

                            <label>Assign to Regional Manager <span class="text-danger">*</span></label>
                            <select name="assign" id="edt-assign" class="form-control regional-manager-select">
                                <option value="">Select Regional Manager</option>
                                @foreach($regional_managers as $regional)
                                <option value="{{ $regional->id }}">{{ $regional->name }}</option>
                                @endforeach
                            </select>
                            <div class="region-info-badges" id="edit-region-badges"></div>
                            <span class="assign-error text-danger"></span>
                        </div>
                        <div>
                            <label>Phone</label>
                            <input type="number" name="phone" id="edt-phone">
                            <span class="phone-error text-danger"></span>

                            <label>Address</label>
                            <input type="text" name="address" id="edt-address">
                            <span class="address-error text-danger"></span>

                            <div class="form-group">
                                <label>Assign States <span class="text-danger">*</span></label>
                                <div class="checkbox-dropdown" id="edit-area-dropdown-container">
                                    <div class="checkbox-dropdown-text">Select States...</div>
                                    <div class="checkbox-dropdown-list" id="edit-area-checkbox-list"></div>
                                </div>
                                <select name="areas[]" id="edit-real-area-select" multiple style="display:none;"></select>
                                <span class="areas-error text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary update-btn">Update Area Manager</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        
        function showLoader() { $('#loader-overlay').show(); }
        function hideLoader() { $('#loader-overlay').hide(); }
        function clearErrors() { $('.text-danger').text('').hide(); }

        // --- FETCH STATES LOGIC ---
        function fetchStatesForManager(managerId, targetListId, targetTextId, targetBadgesId, selectedStates = [], userId = null) {
            
            var listContainer = $(targetListId);
            var textDisplay = $(targetTextId);
            var badgesContainer = $(targetBadgesId);
            var hiddenSelect = listContainer.closest('.form-group').find('select');

            listContainer.empty();
            hiddenSelect.empty();
            badgesContainer.empty();
            textDisplay.text('Loading...');

            $.ajax({
                url: '/get-states-by-manager',
                method: 'GET',
                data: { manager_id: managerId, user_id: userId },
                success: function(response) {
                    
                    // 1. Show Manager's Region Badges
                    if(response.manager_regions.length > 0) {
                        response.manager_regions.forEach(function(r) {
                            badgesContainer.append('<span class="region-badge">'+r.name+'</span>');
                        });
                    } else {
                        badgesContainer.html('<small class="text-muted">No regions assigned to this manager.</small>');
                    }

                    // 2. Build Grouped State List
                    var hasStates = false;
                    $.each(response.grouped_states, function(regionName, states) {
                        hasStates = true;
                        // Add Header
                        listContainer.append('<div class="dropdown-header">' + regionName + '</div>');
                        
                        // Add States
                        $.each(states, function(i, state) {
                            var isChecked = selectedStates.some(id => id == state.id) ? 'checked' : '';
                            var isSelectedClass = isChecked ? 'is-selected' : '';

                            var item = `
                                <label class="checkbox-dropdown-item ${isSelectedClass}">
                                    <input type="checkbox" class="area-checkbox" value="${state.id}" ${isChecked}>
                                    ${state.name}
                                </label>`;
                            listContainer.append(item);

                            if(isChecked) {
                                hiddenSelect.append(new Option(state.name, state.id, true, true));
                            }
                        });
                    });

                    // Update Text
                    if(!hasStates) {
                        textDisplay.text('No available states found.');
                    } else {
                        updateDropdownText(hiddenSelect, textDisplay);
                    }
                },
                error: function() {
                    textDisplay.text('Error fetching data.');
                }
            });
        }

        function updateDropdownText(select, display) {
            var count = select.find('option').length;
            if (count === 0) display.text("Select States...");
            else if (count <= 2) {
                var txt = [];
                select.find('option').each(function() { txt.push($(this).text()) });
                display.text(txt.join(', '));
            } else {
                display.text(count + " states selected");
            }
        }

        // --- DROPDOWN UI EVENTS ---
        $(document).on('click', '.checkbox-dropdown', function(e) {
            $('.checkbox-dropdown-list').not($(this).find('.checkbox-dropdown-list')).removeClass('show');
            $(this).find('.checkbox-dropdown-list').toggleClass('show');
            e.stopPropagation();
        });
        $(document).on('click', function() { $('.checkbox-dropdown-list').removeClass('show'); });
        $(document).on('click', '.checkbox-dropdown-list', function(e) { e.stopPropagation(); });

        $(document).on('change', '.area-checkbox', function() {
            var container = $(this).closest('.checkbox-dropdown').parent();
            var hiddenSelect = container.find('select');
            var textDisplay = container.find('.checkbox-dropdown-text');
            var labelItem = $(this).closest('.checkbox-dropdown-item');
            var val = $(this).val();
            var text = $(this).parent().text().trim();

            if($(this).is(':checked')) {
                hiddenSelect.append(new Option(text, val, true, true));
                labelItem.addClass('is-selected');
            } else {
                hiddenSelect.find("option[value='"+val+"']").remove();
                labelItem.removeClass('is-selected');
            }
            updateDropdownText(hiddenSelect, textDisplay);
        });

        // --- ADD FORM EVENTS ---
        $('#assign').change(function() {
            var id = $(this).val();
            if(id) {
                fetchStatesForManager(id, '#area-checkbox-list', '#area-dropdown-container .checkbox-dropdown-text', '#add-region-badges');
            } else {
                $('#area-checkbox-list').empty();
                $('#add-region-badges').empty();
                $('#area-dropdown-container .checkbox-dropdown-text').text('Select Manager First');
            }
        });

        $('#add-area-manager').submit(function(e) {
            e.preventDefault();
            clearErrors();
            showLoader();
            $.ajax({
                url: "{{ route('area_manager.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(res) {
                    hideLoader();
                    if(res.errors) {
                        for(var f in res.errors) $('.'+f.split('.')[0]+'-error').text(res.errors[f][0]).show();
                    } else location.reload();
                }
            });
        });

        // --- EDIT FORM EVENTS ---
        $('.edit-area-manager-btn').click(function() {
            var id = $(this).data('id');
            clearErrors();
            showLoader();

            $.ajax({
                url: '/edit-area-manager/' + id,
                type: 'GET',
                success: function(res) {
                    hideLoader();
                    $('#edt-name').val(res.name);
                    $('#edt-email').val(res.email);
                    $('#edt-phone').val(res.phone);
                    $('#edt-address').val(res.address);
                    $('#edt-assign').val(res.regional_manager_id);
                    $('.update-btn').data('id', res.user_id);

                    // Fetch states with pre-selection logic
                    fetchStatesForManager(
                        res.regional_manager_id, 
                        '#edit-area-checkbox-list', 
                        '#edit-area-dropdown-container .checkbox-dropdown-text', 
                        '#edit-region-badges', 
                        res.area_ids, 
                        res.user_id
                    );

                    $('#edit-area-manager').modal('show');
                },
                error: function() { hideLoader(); alert('Error'); }
            });
        });

        $('#edt-assign').change(function() {
            var id = $(this).val();
            if(id) {
                fetchStatesForManager(id, '#edit-area-checkbox-list', '#edit-area-dropdown-container .checkbox-dropdown-text', '#edit-region-badges', [], $('.update-btn').data('id'));
            }
        });

        $('.update-btn').click(function(e) {
            e.preventDefault();
            clearErrors();
            showLoader();
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('area_manager.update', ':id') }}".replace(':id', id),
                method: 'POST',
                data: $('#update-area-manager').serialize(),
                success: function(res) {
                    hideLoader();
                    if(res.errors) {
                        for(var f in res.errors) $('.'+f.split('.')[0]+'-error').text(res.errors[f][0]).show();
                    } else location.reload();
                }
            });
        });

        // Delete Logic (Keep existing)
        $('.delete-btn').click(function(e) {
            e.preventDefault();
            if(confirm('Are you sure?')) $(this).closest('form').submit();
        });

        // --- SEARCH: filter by name, email, state ---
        $('.dental-office-search').on('keyup', function() {
            var filter = $(this).val().toUpperCase();
            $('.dental-office-table tbody tr').each(function() {
                var name = $(this).find('td:eq(1)').text().toUpperCase();
                var email = $(this).find('td:eq(2)').text().toUpperCase();
                var states = $(this).find('td:eq(4)').text().toUpperCase();
                if (name.indexOf(filter) > -1 || email.indexOf(filter) > -1 || states.indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

    });
</script>
@endsection