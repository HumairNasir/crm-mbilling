@extends('layouts.backend')

@section('content')
<style>
    /* --- CHECKBOX DROPDOWN STYLES --- */
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
        max-height: 100px;
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

    /* GREEN BACKGROUND FOR SELECTED ITEMS */
    .checkbox-dropdown-item.is-selected {
        background-color: #d4edda;
        color: #155724;
    }

    .checkbox-dropdown-item.is-selected:hover {
        background-color: #c3e6cb;
    }

    .checkbox-dropdown-item input {
        margin-right: 10px;
        transform: scale(1.2);
    }
</style>

<div class="content-main dental-office-content">
    <h3>Regional Managers</h3>
    <div class="">
        <div class="dental-office-parent">
            <div class="search-main search-employee">
                <input type="search" name="search" id="search" placeholder="Search name, email, region..." class="dental-office-search">
                <img src="../images/search.svg" alt="">
            </div>
            <div class="category-search search-employee dental-category">
                <select name="cats" id="categories">
                    <option value="" disabled selected>Sorted By</option>
                    <option value="dummy1">All</option>
                    <option value="dummy1">Recently Added</option>
                </select>
            </div>
            <div class="add-dental-button">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">Add Regional Manager</button>
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
                        <td class="dp-none">Address</td>
                        <td class="dp-none">Regions</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($regional_managers as $manager)
                    @php
                    $name = !empty($manager->name) ? $manager->name : "-";
                    $email = !empty($manager->email) ? $manager->email : "-";
                    $phone = !empty($manager->phone) ? $manager->phone : "-";
                    $address = !empty($manager->address) ? $manager->address : "-";
                    @endphp
                    <tr>
                        <td class="dp-none">{{ $loop->index + 1 }}</td>
                        <td>
                            <div class="dental-office-name-img">
                                <img src="../images/img.svg" alt="">
                                <h6>{{$name}}</h6>
                            </div>
                        </td>
                        <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">{{ $email }}</button></a></td>
                        <td class="dp-none"><span>{{ $phone }}</span></td>
                        <td class="dp-none"><span>{{ $address }}</span></td>

                        <td class="dp-none">
                            @if($manager->regions->count() > 0)
                            @foreach($manager->regions as $reg)
                            <span class="badge badge-info" style="background-color: #28a745; color: white; padding: 5px 8px; border-radius: 4px; font-size: 11px; margin-right: 3px;">
                                {{ $reg->name }}
                            </span>
                            @endforeach
                            @else
                            -
                            @endif
                        </td>

                        <td class="action-icons">
                            <div class='d-flex align-items-center'>

                                <form action="" method="POST" id="edit-form-{{ $manager->id }}" class="edit-form m-0">
                                    @csrf
                                    @method('Update')
                                    <button class="btn" type="button">
                                        <img style="background: #e0e0e0; padding: 5px; border-radius: 4px;" src="../images/pencil.svg" alt="" title="Edit" data-id="{{ $manager->id }}" class="edit-manager-btn">
                                    </button>
                                </form>

                                <form action="{{ route('regional_manager.destroy', $manager->id) }}" method="POST" id="delete-form-{{ $manager->id }}" class="delete-form m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn delete-btn" data-id="{{ $manager->id }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('Delete') }}">
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

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Regional Manager</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" id="add-regional-manager">
                <div class="modal-body">
                    @csrf
                    <div class="form-office-main">
                        <div>
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" placeholder="John">
                            <span class="name-error text-danger"></span>

                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" placeholder="abc@example.com">
                            <span class="email-error text-danger"></span>

                            <label for="password">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" placeholder="*******">
                            <span class="password-error text-danger"></span>
                        </div>

                        <div>
                            <label for="phone">Phone</label>
                            <input type="number" name="phone" placeholder="+123 456 789">
                            <span class="phone-error text-danger"></span>

                            <label for="address">Address</label>
                            <input type="text" name="address" placeholder="Address">
                            <span class="address-error text-danger"></span>

                            <div class="form-group">
                                <label>Assign Regions <span class="text-danger">*</span></label>

                                <div class="checkbox-dropdown" id="add-region-dropdown">
                                    <div class="checkbox-dropdown-text">Select Regions...</div>
                                    <div class="checkbox-dropdown-list">
                                        @foreach($available_regions as $region)
                                        <label class="checkbox-dropdown-item">
                                            <input type="checkbox" class="region-checkbox" value="{{ $region->id }}">
                                            {{ $region->name }}
                                        </label>
                                        @endforeach

                                        @if($available_regions->isEmpty())
                                        <div style="padding:10px; color:#999; font-size:13px; text-align:center;">All regions are assigned.</div>
                                        @endif
                                    </div>
                                </div>
                                <select name="region[]" id="real-region-select" multiple style="display:none;"></select>
                                <span class="region-error text-danger"></span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer form-office-modal-footer">
                    <button type="submit" class="btn btn-primary">Add Regional Manager</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="edit-regional-manager" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle3" aria-hidden="true">
    <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle3">Edit Regional Manager</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="POST" id="update-regional-manager">
                    <div class="modal-body">
                        @csrf
                        <div class="form-office-main">
                            <div>
                                <label for="edt-name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="edt-name">
                                <span class="name-error text-danger"></span>

                                <label for="edt-email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="edt-email">
                                <span class="email-error text-danger"></span>

                                <label for="edt-password">Password (Leave blank to keep)</label>
                                <input type="password" name="password" id="edt-password">
                                <span class="password-error text-danger"></span>
                            </div>

                            <div>
                                <label for="edt-phone">Phone</label>
                                <input type="number" name="phone" id="edt-phone">
                                <span class="phone-error text-danger"></span>

                                <label for="edt-address">Address</label>
                                <input type="text" name="address" id="edt-address">
                                <span class="address-error text-danger"></span>

                                <div class="form-group">
                                    <label>Assign Regions <span class="text-danger">*</span></label>

                                    <div class="checkbox-dropdown" id="edit-region-dropdown">
                                        <div class="checkbox-dropdown-text">Select Regions...</div>
                                        <div class="checkbox-dropdown-list"></div>
                                    </div>
                                    <select name="region[]" id="edit-real-region-select" multiple style="display:none;"></select>
                                    <span class="region-error text-danger"></span>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer form-office-modal-footer">
                        <button type="submit" class="btn btn-primary update-btn whitespace-nowrap" data-id="">Update Regional Manager</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function showLoader() {
            var loader = document.getElementById('loader-overlay');
            if (loader) loader.style.display = 'block';
        }

        function hideLoader() {
            var loader = document.getElementById('loader-overlay');
            if (loader) loader.style.display = 'none';
        }

        function clearErrors() {
            $('.text-danger').text('').hide();
        }

        // ==========================================
        //  CUSTOM DROPDOWN LOGIC
        // ==========================================

        $(document).on('click', '.checkbox-dropdown', function(e) {
            $('.checkbox-dropdown-list').not($(this).find('.checkbox-dropdown-list')).removeClass('show');
            $(this).find('.checkbox-dropdown-list').toggleClass('show');
            e.stopPropagation();
        });

        $(document).on('click', function() {
            $('.checkbox-dropdown-list').removeClass('show');
        });

        $(document).on('click', '.checkbox-dropdown-list', function(e) {
            e.stopPropagation();
        });

        // Handle Checkbox Selection
        $(document).on('change', '.region-checkbox', function() {
            var container = $(this).closest('.checkbox-dropdown').parent();
            var hiddenSelect = container.find('select');
            var textDisplay = container.find('.checkbox-dropdown-text');
            var labelItem = $(this).closest('.checkbox-dropdown-item');

            var value = $(this).val();
            var text = $(this).parent().text().trim();

            if ($(this).is(':checked')) {
                if (hiddenSelect.find("option[value='" + value + "']").length == 0) {
                    hiddenSelect.append(new Option(text, value, true, true));
                }
                labelItem.addClass('is-selected');
            } else {
                hiddenSelect.find("option[value='" + value + "']").remove();
                labelItem.removeClass('is-selected');
            }

            var selectedCount = hiddenSelect.find('option').length;
            if (selectedCount === 0) {
                textDisplay.text("Select Regions...");
            } else if (selectedCount <= 3) {
                var selectedText = [];
                hiddenSelect.find('option').each(function() {
                    selectedText.push($(this).text());
                });
                textDisplay.text(selectedText.join(', '));
            } else {
                textDisplay.text(selectedCount + " regions selected");
            }
        });

        // ==========================================
        //  AJAX ACTIONS
        // ==========================================

        $('#add-regional-manager').submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            clearErrors();
            showLoader();

            $.ajax({
                url: "{{ route('regional_manager.store') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    hideLoader();
                    if (response.errors) {
                        for (var field in response.errors) {
                            var cleanField = field.split('.')[0];
                            $('.' + cleanField + '-error').text(response.errors[field][0]).show();
                        }
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    console.error(xhr);
                }
            });
        });

        // EDIT LOGIC (UPDATED: Builds dropdown from 'valid_regions')
        $('.edit-manager-btn').click(function() {
            var managerId = $(this).data('id');
            clearErrors();
            showLoader();

            $.ajax({
                url: '/edit-manager/' + managerId,
                type: 'GET',
                success: function(response) {
                    hideLoader();

                    $('#edit-regional-manager #edt-name').val(response.name);
                    $('#edit-regional-manager #edt-email').val(response.email);
                    $('#edit-regional-manager #edt-phone').val(response.phone);
                    $('#edit-regional-manager #edt-address').val(response.address);
                    $('.update-btn').data('id', response.user_id);

                    // --- REBUILD DROPDOWN FOR EDIT ---
                    var editList = $('#edit-region-dropdown .checkbox-dropdown-list');
                    var hiddenSelect = $('#edit-real-region-select');
                    var textDisplay = $('#edit-region-dropdown .checkbox-dropdown-text');

                    editList.empty(); // Clear old list
                    hiddenSelect.empty();
                    textDisplay.text('Select Regions...');

                    // Loop through VALID regions sent from Controller
                    if (response.valid_regions && response.valid_regions.length > 0) {
                        response.valid_regions.forEach(function(region) {

                            // Check if this region is assigned to CURRENT user
                            var isChecked = response.region_ids.includes(region.id) ? 'checked' : '';
                            var isSelectedClass = isChecked ? 'is-selected' : '';

                            // Create HTML Item
                            var item = `
                                <label class="checkbox-dropdown-item ${isSelectedClass}">
                                    <input type="checkbox" class="region-checkbox" value="${region.id}" ${isChecked}>
                                    ${region.name}
                                </label>
                            `;
                            editList.append(item);

                            // Sync Hidden Select
                            if (isChecked) {
                                hiddenSelect.append(new Option(region.name, region.id, true, true));
                            }
                        });

                        // Update text label after rebuild
                        var selectedCount = hiddenSelect.find('option').length;
                        if (selectedCount > 0) {
                            if (selectedCount <= 3) {
                                var txt = [];
                                hiddenSelect.find('option').each(function() {
                                    txt.push($(this).text())
                                });
                                textDisplay.text(txt.join(', '));
                            } else {
                                textDisplay.text(selectedCount + " regions selected");
                            }
                        }

                    } else {
                        editList.append('<div style="padding:10px; color:#999; text-align:center;">No available regions</div>');
                    }

                    $('#edit-regional-manager').modal('show');
                },
                error: function(xhr) {
                    hideLoader();
                    console.error(xhr);
                }
            });
        });

        $('.update-btn').click(function(e) {
            e.preventDefault();
            var userId = $(this).data('id');
            var formData = $('#update-regional-manager').serialize();

            clearErrors();
            showLoader();

            $.ajax({
                url: "{{ route('regional_manager.update', ':userId') }}".replace(':userId', userId),
                method: 'POST',
                data: formData,
                success: function(response) {
                    hideLoader();
                    if (response.errors) {
                        for (var field in response.errors) {
                            var cleanField = field.split('.')[0];
                            $('.' + cleanField + '-error').text(response.errors[field][0]).show();
                        }
                    } else {
                        location.reload();
                    }
                },
                error: function(xhr) {
                    hideLoader();
                    console.error(xhr);
                }
            });
        });

        $('.delete-btn').click(function(event) {
            event.preventDefault();
            var formId = $(this).closest('form').attr('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#' + formId).submit();
                }
            });
        });

        // --- SEARCH: filter by region, name, email ---
        $('.dental-office-search').on('keyup', function() {
            var filter = $(this).val().toUpperCase();
            $('.dental-office-table tbody tr').each(function() {
                var name = $(this).find('td:eq(1)').text().toUpperCase();
                var email = $(this).find('td:eq(2)').text().toUpperCase();
                var regions = $(this).find('td:eq(5)').text().toUpperCase();
                if (name.indexOf(filter) > -1 || email.indexOf(filter) > -1 || regions.indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

    });
</script>
@endsection