
      @extends('layouts.backend')
@section('content')
<style>
    .error-field {
        border-color: red;
        border-width: 5px;

    }
</style>
<div class="content-main dental-office-content">
    <h3>Area Managers</h3>
    <div class="">
        <div class="dental-office-parent">
            <div class="search-main search-employee">
                <input type="search" name="search" id="search" placeholder="Search..." class="dental-office-search">
                <img src="../images/search.svg" alt="">
            </div>
            <div class="category-search search-employee dental-category">
                <select name="cats" id="categpries">
                    <option value="" disabled selected>Sorted by</option>
                    <option value="dummy1">Dummy1</option>
                    <option value="dummy1">Dummy1</option>
                    <option value="dummy1">Dummy1</option>
                    <option value="dummy1">Dummy1</option>
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
                    <td class="dp-none">Address</td>
                    <td>Action</td>
                </tr>
                </thead>
                <tbody>
                @foreach ($area_managers as $manager)
                    @php
                        $name = !empty($manager->name) ? $manager->name : "-";
                        $email = !empty($manager->email) ? $manager->email : "-";
                        $phone = !empty($manager->phone) ? $manager->phone : "-";
                        $address = !empty($manager->address) ? $manager->address : "-";
                    @endphp
                    <tr>
                        <td class="dp-none">{{ $loop->index + 1 }}</td>
                        <td class="dental-office-name-img">
                            <img src="../images/img.svg" alt=""><h6>{{$name}}</h6>
                        </td>
                        <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">{{ $email }}</button></a></td>
                        <td class="dp-none"><span>{{ $phone }}</span></td>
                        <td class="dp-none"><span>{{ $address }}</span></td>
                        <td class="action-icons">

                            
                            <form action="{{ route('area_manager.edit', $manager->id) }}" method="POST" id="edit-form-{{ $manager->id }}" class="edit-form">
                                @csrf
                                @method('Update')
                                    
                                    <img src="../images/pencil.svg" alt="" title="Edit" data-id="{{ $manager->id }}" class="edit-area-manager-btn">

                            </form>
                            <form action="{{ route('area_manager.destroy', $manager->id) }}" method="POST" id="delete-form-{{ $manager->id }}" class="delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger delete-btn" data-id="{{ $manager->id }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('Delete') }}">
                                    <img src="../images/trash.svg" alt="">
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach

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
        <h5 class="modal-title" id="exampleModalLongTitle">Add Area Manager</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="POST" id="add-area-manager">
            @csrf
            <div class="form-office-main">
                <div>
                    <label for="name">Namedd</label>
                    <input type="text" name="name" id="name" placeholder="John">
                    <span class="name-error text-danger"></span>

                    <label for="name">Email</label>
                    <input type="email" name="email" id="email" placeholder="abc@example.com">
                    <span class="email-error text-danger"></span>


                    <label for="country">Country</label>
                    <select name="country" id="country">
                        <option value="US">United States</option>
                    </select>
                    <span class="country-error text-danger"></span>

                    <label for="region">Region</label>
                    <select name="region" id="region">
                        <option value="">Select Region</option>
                        @foreach($region_list as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                    <span class=" region-error text-danger"></span>



                    <label for="name">Password</label>
                    <input type="password" name="password" id="password" placeholder="*******">
                    <span  class="password-error text-danger"></span>

                </div>
                <div>

                    <label for="name">Phone</label>
                    <input type="number" name="phone" id="phone" placeholder="+123 456 789">
                    <span id=""  class="phone-error text-danger"></span>

                
                    <label for="name">Address</label>
                    <input type="text" name="address" id="address" placeholder="Address">
                    <span class="address-error text-danger"></span>

                   
                    <label for="assign">Assign to</label>
                    <select name="assign" id="assign">
                        <option value="">Select regional manager</option>
                        @foreach($regional_managers as $regional)
                            <option value="{{ $regional->id }}">{{ $regional->name }}</option>
                        @endforeach
                    </select>
                    <span class="assign-error text-danger"></span>


                    <label for="area">Area</label>
                    <select name="area" id="area">
                        <option value="">Select Area</option>
                    </select>
                    <span class="area-error text-danger"></span>

                </div>
            </div>
            <div class="modal-footer form-office-modal-footer">
                <button type="submit" class="btn btn-secondary" >Add Area Manager</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </form>
      </div>
      
    </div>
  </div>
</div>

<!-- Modal 2 -->

<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle2" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle2">Add Note</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="">
          <div class="form-office-main">
            <div>
              <label for="contact-way">How they were contacted</label>
              <input type="tel" name="contact-way" placeholder="Phone">
              <label for="product-purchase">Did the dental office purchese the product</label>
              <input type="text" name="product-purchase" placeholder="No">
              <label for="contact-date">Contact date</label>
              <input type="text" name="contact-date" placeholder="01/01/2024">
            </div>
            <div>
              <label for="sale-rep">How receptive the dental office is to sale</label>
              <input type="text" name="sale-rep" placeholder="Cold">
              <label for="followup">Follow up date</label>
              <input type="text" name="followup" placeholder="12/02/2024">
              <label for="contact-person">Contact person</label>
              <input type="text" name="contact-person" placeholder="John Morgan">
            </div>
            <div class="text-area-parent">
                          <label for="description">Description</label>
        <textarea name="description" class="description-textarea" cols="30" rows="10"></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer form-office-modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal 3 -->

<div class="modal fade" id="edit-area-manager" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle3" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle3">Edit Area Manager</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="" method="POST" id="update-area-manager">

        @csrf
            <div class="form-office-main">
                <div>
                    <label for="name">Name</label>
                    <input type="text" name="name" id="edt-name" placeholder="John">
                    <span class="name-error text-danger"></span>


                    <label for="name">Email</label>
                    <input type="email" name="email" id="edt-email" placeholder="abc@example.com">
                    <span class="email-error text-danger"></span>


                    <label for="country">Country</label>

                    <select name="country" id="country">
                        <option value="US">United States</option>
                    </select>
                    <span class="country-error text-danger"></span>

                    <label for="region">Region</label>
                    <select name="region" id="edt-region">
                        <option value="">Select Region</option>
                        @foreach($region_list as $region)
                            <option value="{{ $region->id }}">{{ $region->name }}</option>
                        @endforeach
                    </select>
                    <span class="region-error text-danger"></span>

                    <label for="name">Password</label>
                    <input type="password" name="password" placeholder="*******">
                    <span class="password-error text-danger"></span>


                </div>
                <div>
                    <label for="name">Phone</label>
                    <input type="number" name="phone" id="edt-phone" placeholder="+123 456 789">
                    <span class="phone-error text-danger"></span>

                
                    <label for="name">Address</label>
                    <input type="text" name="address" id="edt-address" placeholder="Address">
                    <span class="address-error text-danger"></span>

                   
                    <label for="assign">Assign to</label>
                    <select name="assign" id="edt-assign">
                        <option value="">Select regional manager</option>
                        @foreach($regional_managers as $regional)
                            <option value="{{ $regional->id }}">{{ $regional->name }}</option>
                        @endforeach
                    </select>
                    <span class="assign-error text-danger"></span>


                    <label for="area">Area</label>
                    <select name="area" id="edt-area">
                        <option value="">Select Area</option>
                    </select>
                    <span class="area-error text-danger"></span>

                </div>
            </div>
            <div class="modal-footer form-office-modal-footer">
                <button type="submit" class="btn btn-secondary update-btn" >Update Area Manager</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </form>
      </div>
     
    </div>
  </div>
</div>


@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>


<script>
    $(document).ready(function() {

        $('#add-area-manager').submit(function(event) {
            event.preventDefault(); 
            var formData = $(this).serialize();
            $.ajax({
                url: "{{ route('area_manager.store') }}",  
                method: 'POST',  
                data: formData,  
                success: function(response) {
                    var errors = response.errors;
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
                    console.error("error");
                }
            });
        });

        $('.edit-area-manager-btn').click(function() {
            var managerId = $(this).data('id');   
            console.log("arslan");
            console.log(managerId);
            clearErrors();
            $.ajax({
 
                url: '/edit-area-manager/' + managerId,
                type: 'GET',
                success: function(response) {
                    console.log(response.name);
                    console.log(response);
                    $('#edit-area-manager #edt-name').val(response.name);
                    $('#edit-area-manager #edt-phone').val(response.phone);
                    $('#edit-area-manager #edt-email').val(response.email);
                    $('#edit-area-manager #edt-address').val(response.address);
                    
                    var button = $('.update-btn');
                    button.attr('data-id', response.user_id);
                    regionId = response.region_id;
                    regional_manager_id = response.regional_manager_id;
                    regionId = response.region_id;
                    area_list = response.area_list;
                    area_id = response.area_id;
                    $('#edt-region option').each(function() {
                        if ($(this).val() == regionId) {
                            $(this).prop('selected', true);
                        } else {
                            $(this).prop('selected', false);
                        }
                    });

                    $('#edt-assign option').each(function() {
                        if ($(this).val() == regional_manager_id) {
                            $(this).prop('selected', true);
                        } else {
                            $(this).prop('selected', false);
                        }
                    });

                     $('#area').append($('<option>', {
                        value: '',
                        text: 'Select Areass'
                    }));
                    // Clear existing options
                        $('#edt-area').empty();

                        // Append default option
                        $('#edt-area').append($('<option>', {
                            value: '',
                            text: 'Select Area'
                        }));

                        // Append options from area_list
                        $.each(response.area_list, function(index, area) {
                            $('#edt-area').append($('<option>', {
                                value: area.id,
                                text: area.name
                            }));
                        });

                        console.log(response.area_list);
                    $('#edt-area').val(response.area_id);


                    $('#edit-area-manager').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        $('#region').change(function() {
            var region_id = $(this).val();
             
            $.ajax({
                url: '/get-areas/' + region_id,
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#area').empty(); // Clear previous options
                    $('#area').append($('<option>', {
                        value: '',
                        text: 'Select Area'
                    }));
                    $.each(response, function(index, area) {
                        $('#area').append($('<option>', {
                            value: area.id,
                            text: area.name
                        }));
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        $('#edt-region').change(function() {
            var region_id = $(this).val();
             
            $.ajax({
                url: '/get-areas/' + region_id,
                method: 'GET',
                success: function(response) {
                    console.log(response);
                    $('#edt-area').empty(); // Clear previous options
                    $('#edt-area').append($('<option>', {
                        value: '',
                        text: 'Select Area'
                    }));
                    $.each(response, function(index, area) {
                        $('#edt-area').append($('<option>', {
                            value: area.id,
                            text: area.name
                        }));
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        // $('#area').change(function() {
        //     var area_id = $(this).val();
             
        //     console.log(area_id);
             
        //     $.ajax({
        //         url: '/get-territories/' + area_id,
        //         method: 'GET',
        //         success: function(response) {
        //             console.log("response");
        //             console.log(response);
        //             $('#territory').empty(); // Clear previous options
        //             $('#territory').append($('<option>', {
        //                 value: '',
        //                 text: 'Select Area'
        //             }));
        //             $.each(response, function(index, area) {
        //                 $('#territory').append($('<option>', {
        //                     value: area.id,
        //                     text: area.name
        //                 }));
        //             });
        //         },
        //         error: function(xhr, status, error) {
        //             console.error(error);
        //         }
        //     });
        // });
        $('.update-btn').click(function(e) {
            e.preventDefault(); // Prevent the default form submission

            var userId = $(this).data('id');
        
            // Set the user_id value in the hidden input field
             
            // Serialize the form data
            var formData = $('#update-area-manager').serialize();

            console.log(userId);
            console.log(formData);

         
 
            $.ajax({
                 url: "{{ route('area_manager.update', ':userId') }}".replace(':userId', userId),  
                method: 'POST',  
                data: formData,  
                success: function(response) {
                    var errors = response.errors;
                    console.log(errors);

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
            event.preventDefault(); 
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