@extends('layouts.backend')
@section('content')
<div class="content-main dental-office-content">
  <h3>Dental Offices</h3>
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
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">Add Dental Office</button>
      </div>
    </div>
    <div class="dental-table-main">
      <table class="dental-office-table">
        <thead class="dental-table-header">
          <tr>
            <td class="dp-none">#</td>
            <td>Office Name</td>
            <td>Email</td>
            <td class="dp-none">Response</td>
            <td class="dp-none">Territory</td>
            <td class="dp-none">Sales Person</td>
            <td>Note</td>
            <td>Action</td>
          </tr>
        </thead>
        <tbody>
          @forelse($dentalOffices as $index => $office)
          <tr>
            <td class="dp-none">{{ $dentalOffices->firstItem() + $index }}</td>
            <td class="dental-office-name-img">
              <img src="../images/img.svg" alt="">
              <h6>{{ $office->name ?? 'N/A' }}</h6>
            </td>
            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
            <td class="dp-none"><span>{{ $office->receptive ?? 'N/A' }}</span></td>
            <td class="dp-none"><span>{{ $office->territory->name ?? 'N/A' }}</span></td>
            <td class="dp-none"><span>{{ $office->salesRep->name ?? 'N/A' }}</span></td>
            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
            <td class="action-icons">
              <svg data-toggle="modal" data-target="#viewModal{{ $office->id }}" style="cursor: pointer; width: 20px; height: 20px; display: inline-block;" title="View Details" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
              <img src="../images/trash.svg" alt="" title="Delete">
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" style="text-align: center;">No dental offices found</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="main-pagination">
      <div>
        Showing <span>{{ $dentalOffices->firstItem() ?? 0 }}</span> - <span>{{ $dentalOffices->lastItem() ?? 0 }}</span> out of <span>{{ $dentalOffices->total() }}</span>
      </div>
      <div class="pagination">
        @if ($dentalOffices->onFirstPage())
          <a href="#" class="disabled">Prev</a>
        @else
          <a href="{{ $dentalOffices->previousPageUrl() }}">Prev</a>
        @endif

        @php
          $currentPage = $dentalOffices->currentPage();
          $lastPage = $dentalOffices->lastPage();
          $start = max(1, $currentPage - 2);
          $end = min($lastPage, $currentPage + 2);
        @endphp

        @if ($start > 1)
          <a href="{{ $dentalOffices->url(1) }}">1</a>
          @if ($start > 2)
            <span style="padding: 8px 12px;">...</span>
          @endif
        @endif

        @for ($page = $start; $page <= $end; $page++)
          @if ($page == $currentPage)
            <a href="#" class="active">{{ $page }}</a>
          @else
            <a href="{{ $dentalOffices->url($page) }}">{{ $page }}</a>
          @endif
        @endfor

        @if ($end < $lastPage)
          @if ($end < $lastPage - 1)
            <span style="padding: 8px 12px;">...</span>
          @endif
          <a href="{{ $dentalOffices->url($lastPage) }}">{{ $lastPage }}</a>
        @endif

        @if ($dentalOffices->hasMorePages())
          <a href="{{ $dentalOffices->nextPageUrl() }}">Next</a>
        @else
          <a href="#" class="disabled">Next</a>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- View Details Modals -->
@foreach($dentalOffices as $office)
<div class="modal fade" id="viewModal{{ $office->id }}" tabindex="-1" role="dialog" aria-labelledby="viewModalTitle{{ $office->id }}" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalTitle{{ $office->id }}">Dental Office Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-office-main">
          <div>
            <label><strong>Office Name:</strong></label>
            <p>{{ $office->name ?? 'N/A' }}</p>
            
            <label><strong>Email:</strong></label>
            <p>{{ $office->email ?? 'N/A' }}</p>
            
            <label><strong>Phone:</strong></label>
            <p>{{ $office->phone ?? 'N/A' }}</p>
            
            <label><strong>Country:</strong></label>
            <p>{{ $office->country ?? 'N/A' }}</p>
            
            <label><strong>Region:</strong></label>
            <p>{{ $office->region->name ?? 'N/A' }}</p>
            
            <label><strong>State/Area:</strong></label>
            <p>{{ $office->state->name ?? 'N/A' }}</p>
          </div>
          <div>
            <label><strong>Territory:</strong></label>
            <p>{{ $office->territory->name ?? 'N/A' }}</p>
            
            <label><strong>Sales Representative:</strong></label>
            <p>{{ $office->salesRep->name ?? 'N/A' }}</p>
            
            <label><strong>Contact Person:</strong></label>
            <p>{{ $office->contact_person ?? 'N/A' }}</p>
            
            <label><strong>Response Level:</strong></label>
            <p>{{ $office->receptive ?? 'N/A' }}</p>
            
            <label><strong>Contact Source:</strong></label>
            <p>{{ $office->contacted_source ?? 'N/A' }}</p>
            
            <label><strong>Contact Date:</strong></label>
            <p>{{ $office->contact_date ?? 'N/A' }}</p>
          </div>
          <div style="width: 100%; margin-top: 15px;">
            <label><strong>Follow Up Date:</strong></label>
            <p>{{ $office->follow_up_date ?? 'N/A' }}</p>
            
            <label><strong>Purchased Product:</strong></label>
            <p>{{ $office->purchase_product ?? 'N/A' }}</p>
            
            <label><strong>Description:</strong></label>
            <p>{{ $office->description ?? 'N/A' }}</p>
          </div>
        </div>
      </div>
      <div class="modal-footer form-office-modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endforeach

<!-- Modal 1 -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add Dental Office</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="">
          <div class="form-office-main">
            <div>
              <label for="name">Name</label>
              <input type="text" name="name" placeholder="John">
              <label for="country">Country</label>
              <input type="text" name="country" placeholder="Finland">
              <label for="area">Area</label>
              <input type="text" name="area" placeholder="Lapland">
            </div>
            <div>
              <label for="assign">Assign to</label>
              <input type="text" name="assign" placeholder="Jack">
              <label for="region">Region</label>
              <input type="text" name="region" placeholder="Uusimaa">
              <label for="territory">Territory</label>
              <input type="text" name="territory" placeholder="Greenland">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer form-office-modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Add Dental Office</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
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
              <select class="form-control response_drop" name="sale-rep" id="categories">
                <option value="" disabled selected>Select Response</option>
                <option value="cold">Cold</option>
                <option value="hot">Hot</option>
                <option value="warm">Warm</option>
              </select>
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

<div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle3" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle3">Edit Dental Office</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="">
          <div class="form-office-main">
            <div>
              <label for="name">Name</label>
              <input type="text" name="name" placeholder="John">
              <label for="country">Country</label>
              <input type="text" name="country" placeholder="Finland">
              <label for="area">Area</label>
              <input type="text" name="area" placeholder="Lapland">
            </div>
            <div>
              <label for="assign">Assign to</label>
              <input type="text" name="assign" placeholder="Jack">
              <label for="region">Region</label>
              <input type="text" name="region" placeholder="Uusimaa">
              <label for="territory">Territory</label>
              <input type="text" name="territory" placeholder="Greenland">
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer form-office-modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Save Changes</button>
      </div>
    </div>
  </div>
</div>



<style>
/* Pagination styling fix */
.main-pagination .pagination {
  display: flex;
  gap: 5px;
  align-items: center;
}

.main-pagination .pagination a {
  padding: 8px 12px;
  text-decoration: none;
  border: 1px solid #ddd;
  border-radius: 4px;
  color: #333;
  transition: all 0.3s ease;
}

.main-pagination .pagination a:hover:not(.disabled) {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
}

.main-pagination .pagination a.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
  font-weight: bold;
}

.main-pagination .pagination a.disabled {
  color: #ccc;
  cursor: not-allowed;
  pointer-events: none;
}

/* View Details Modal styling */
.modal-body p {
  margin-bottom: 10px;
  padding: 8px;
  background-color: #f8f9fa;
  border-radius: 4px;
}

.modal-body label {
  margin-top: 10px;
  margin-bottom: 5px;
}

/* Eye icon styling */
.action-icons svg {
  vertical-align: middle;
  margin-right: 5px;
}
</style>

@endsection