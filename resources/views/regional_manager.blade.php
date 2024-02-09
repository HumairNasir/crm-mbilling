
@extends('layouts.backend')
@section('content')
<div class="content-main dental-office-content">
    <h3>Regional Managers</h3>
    <div class="">
        <div class="dental-office-parent">
            <div class="search-main search-employee">
                <input type="search" name="search" id="search" placeholder="Search..." class="dental-office-search">
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
                                <img src="../images/img.svg" alt=""><h6>{{$name}}</h6>
                            </div>
                        </td>
                        <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">{{ $email }}</button></a></td>
                        <td class="dp-none"><span>{{ $phone }}</span></td>
                        <td class="dp-none"><span>{{ $address }}</span></td>
                        <td class="action-icons">
                            <div class='d-flex align-items-center'>

                                <form action="{{ route('regional_manager.edit', $manager->id) }}" method="POST" id="edit-form-{{ $manager->id }}" class="edit-form m-0">
                                    @csrf
                                    @method('Update')
                                        <button class="btn" type="button">
                                            <img src="../images/pencil.svg" alt="" title="Edit" data-id="{{ $manager->id }}" class="edit-manager-btn">
                                        </button>
                                </form>

                                <form action="{{ route('regional_manager.destroy', $manager->id) }}" method="POST" id="delete-form-{{ $manager->id }}" class="delete-form m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn delete-btn" data-id="{{ $manager->id }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('Delete') }}">
                                        <img src="../images/trash.svg" alt="">
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach



                </tbody>
            </table>
        </div>
        <div class="main-pagination">
            <div>
                Showing <span>1</span> - <span>10</span> out of <span>3</span>
            </div>
            <div class="pagination">
                <a href="#">Prev</a>
                <a href="#">1</a>
                <a href="#">2</a>
                <a href="#">3</a>
                <a href="#">Next</a>
            </div>
        </div>
    </div>
</div>
<!-- Modal 1 -->
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
                        <label for="name">Name</label>
                        <input type="text" name="name" placeholder="John">
                        <span class="name-error text-danger"></span>

                        <label for="name">Email</label>
                        <input type="email" name="email" placeholder="abc@example.com">
                        <span class=" email-error text-danger"></span>

                        <label for="country">Country</label>
                        <select name="country" id="country">
                            <option value="US">United States</option>
                        </select>
                        <span class=" country-error text-danger"></span>


                        <label for="name">Password</label>
                        <input type="password" name="password" placeholder="*******">
                        <span class=" password-error text-danger"></span>

                    </div>

                    <div>
                        <label for="name">Phone</label>
                        <input type="number" name="phone" placeholder="+123 456 789">
                        <span class=" phone-error text-danger"></span>


                        <label for="name">Address</label>
                        <input type="text" name="address" placeholder="Address">
                        <span class=" address-error text-danger"></span>


                        <label for="region">Region</label>
                        <select name="region" id="region">
                            <option value="">Select Region</option>
                            @foreach($region_list as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                        <span class=" region-error text-danger"></span>

                    </div>
                </div>
            </div>
            <div class="modal-footer form-office-modal-footer">
                <button type="submit" class="btn btn-primary" >Add Regional Manager</button>
             </div>
        </form>
    </div>
  </div>
</div>


<!-- Modal 3 -->

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
                        <label for="name">Name</label>
                        <input type="text" name="name" id="edt-name" placeholder="John">
                        <span class=" name-error text-danger"></span>


                        <label for="name">Email</label>
                        <input type="email" name="email" id="edt-email" placeholder="abc@example.com">
                        <span class=" email-error text-danger"></span>


                        <label for="country">Country</label>
                        <select name="country" id="country">
                            <option value="US">United States</option>
                        </select>
                        <span class=" country-error text-danger"></span>


                        <label for="name">Password</label>
                        <input type="password" name="password" placeholder="*******">
                        <span class=" password-error text-danger"></span>

                    </div>

                    <div>

                        <label for="name">Phone</label>
                        <input type="number" name="phone" id="edt-phone" placeholder="+123 456 789">
                        <span class=" phone-error text-danger"></span>


                        <label for="name">Address</label>
                        <input type="text" name="address" id="edt-address" placeholder="Address">
                        <span class=" address-error text-danger"></span>


                        <label for="region">Region</label>
                        <select name="region" id="edt-region">
                            <option value="">Select Region</option>
                            @foreach($region_list as $region)
                                <option value="{{ $region->id }}">{{ $region->name }}</option>
                            @endforeach
                        </select>
                        <span class=" region-error text-danger"></span>


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
            // Show loader overlay
            document.getElementById('loader-overlay').style.display = 'block';
        }
        $('#add-regional-manager').submit(function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Serialize the form data
            var formData = $(this).serialize();
            clearErrors();

            showLoader();
            $.ajax({
                url: "{{ route('regional_manager.store') }}",
                method: 'POST',
                data: formData,
                success: function(response) {
                    var errors = response.errors;
                    document.getElementById('loader-overlay').style.display = 'none';

                    if(errors){
                          // Iterate over the errors object
                        for (var field in errors) {
                            if (errors.hasOwnProperty(field)) {
                                // Get the field name
                                var fieldName = field;

                                var errorMessage = errors[field][0];

                                $('.' + fieldName + '-error').text(errorMessage).show();
                            }

                        }

                    } else {
                        location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    // Handle the error
                    console.error(error);
                }
            });
        });

        $('.edit-manager-btn').click(function() {
            var managerId = $(this).data('id');
            clearErrors();
            showLoader();
            $.ajax({

                url: '/edit-manager/' + managerId,
                type: 'GET',
                success: function(response) {
                    console.log(response.name);
                    console.log(response);
                    document.getElementById('loader-overlay').style.display = 'none';

                    $('#edit-regional-manager #edt-name').val(response.name);
                    $('#edit-regional-manager #edt-phone').val(response.phone);
                    $('#edit-regional-manager #edt-email').val(response.email);
                    $('#edit-regional-manager #edt-address').val(response.address);

                    var button = $('.update-btn');
                    button.attr('data-id', response.user_id);
                    regionId = response.region_id;
                    $('#edt-region option').each(function() {
                        if ($(this).val() == regionId) {
                            $(this).prop('selected', true);
                        } else {
                            $(this).prop('selected', false);
                        }
                    });

                    $('#edit-regional-manager').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });


        $('.update-btn').click(function(e) {
            e.preventDefault(); // Prevent the default form submission

            var userId = $(this).data('id');


            var formData = $('#update-regional-manager').serialize();

            console.log("userId");
            console.log(formData);
            clearErrors();
            showLoader();
            $.ajax({
                 url: "{{ route('regional_manager.update', ':userId') }}".replace(':userId', userId),
                method: 'POST',
                data: formData,
                success: function(response) {
                    var errors = response.errors;
                    console.log(errors);
                    document.getElementById('loader-overlay').style.display = 'none';

                    if(errors){
                          // Iterate over the errors object
                        for (var field in errors) {
                            if (errors.hasOwnProperty(field)) {
                                // Get the field name
                                var fieldName = field;

                                var errorMessage = errors[field][0];

                                $('.'+ fieldName + '-error').text(errorMessage).show();
                            }

                        }

                    } else {
                        location.reload();
                    }

                 },
                error: function(xhr, status, error) {
                    // Handle the error
                    console.error(error);
                }
            });
        });

        $('.delete-btn').click(function(event) {
            event.preventDefault(); // Prevent the default form submission

            var formId = $(this).closest('form').attr('id'); // Get the ID of the parent form
            var managerId = $(this).data('id'); // Get the manager ID from data attribute

            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, submit the form
                    $('#' + formId).submit();
                }
            });
        });

        function clearErrors() {
            // Loop through each form field
            $('form input, form select').each(function() {
                var fieldName = $(this).attr('name');
                // Hide the error message for this field
                $('.' + fieldName + '-error').hide();
            });
        }

    });
</script>


@endsection
