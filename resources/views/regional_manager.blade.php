       
      @extends('layouts.backend')
@section('content')
<div class="content-main dental-office-content">
    <h3>Reginoal Managers</h3>
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
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">Add Regional Manager</button>
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
                        <tr>
                            <td class="dp-none">1</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">2</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">3</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">4</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">5</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">6</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">7</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">8</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">9</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
                                                <tr>
                            <td class="dp-none">10</td>
                            <td class="dental-office-name-img">
                                <img src="../images/img.svg" alt=""><h6>Heath Care Dental</h6>
                            </td>
                            <td class="email-button"><a href=""><button><img src="../images/email.svg" alt="">Email</button></a></td>
                            <td class="dp-none"><span>Cold</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td class="dp-none"><span>Name</span></td>
                            <td><a href="#" data-toggle="modal" data-target="#exampleModalCenter2"><img src="../images/pencil-square.svg" alt="" data-toggle="tooltip" data-placement="top" title="Add Note"></a></td>
                        <td class="action-icons">
                          <img src="../images/pencil.svg" alt="" title="Edit" data-toggle="modal" data-target="#exampleModalCenter3">
                          <img src="../images/trash.svg" alt="" title="Delete">
                        </td>
                          </tr>
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Add Office</button>
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

<div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle3" aria-hidden="true">
  <div class="modal-dialog form-office-modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle3">Edit Regional Manager</h5>
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


@endsection