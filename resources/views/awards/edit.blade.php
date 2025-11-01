@section('pagescript')

    <script src="{{asset('js/file-upload.js')}}"></script>

    <script src="{{asset('js/typeahead.js')}}"></script>

    <script src="{{asset('js/select2.js')}}"></script>

    <script src="{{asset('js/formpickers.js')}}"></script>

@stop



@extends('layouts.admin')



@section('title', 'Create New Award | '.Session::get('site_name'))



@section('content')
      <!-- partial -->
      <div class="main-panel">          
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              Give an Award
            </h3>
           
          </div>
          
                  <div class="row">
                    <div class="col-12">
                     
                      <form action="{{ route('awards.update', $award->id) }}" method="POST" >
                      @csrf
                     

         <!-- Attendance Date -->
                      
                    <div class="form-group">
                      <label>Awardee Name</label>
                      <select class="form-control" id="name" name="name">
                          <option value="" disabled selected>Please Select Awardee Name</option>
                          @foreach($users as $user)
                              <option value="{{ $user->id }}" {{ $award->user_id == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                          @endforeach
                      </select>
                    </div>

                     <div class="form-group">
                      <label for="award">Award Name:</label>
                     <input type="text" id="award" name="award" value="{{ $award->award }}"  class="form-control" required >
                     </div>
                     <div class="form-group">
                      <label for="gift">Gift:</label>
                     <input type="text" id="gift" name="gift" value="{{ $award->gift }}" class="form-control" required >
                     </div>
                     <div class="form-group">
                      <label for="date">Date:</label>
                     <input type="date" id="date" name="date" value="{{ $award->date }}" class="form-control" required >
                     </div>

                      <div>
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                      </div>
                       
                      </form>
                      
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:../../partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2018 <a href="https://www.urbanui.com/" target="_blank">Urbanui</a>. All rights reserved.</span>
            <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="far fa-heart text-danger"></i></span>
          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  @endsection
  <!-- container-scroller -->
  <!-- plugins:js -->
  <script src="../../Admin/support/js/vendor.bundle.base.js"></script>
  <script src="../../Admin/support/js/vendor.bundle.addons.js"></script>
  <!-- endinject -->
  <!-- inject:js -->
  <script src="../../Admin/js/off-canvas.js"></script>
  <script src="../../Admin/js/hoverable-collapse.js"></script>
  <script src="../../Admin/js/misc.js"></script>
  <script src="../../Admin/js/settings.js"></script>
  <script src="../../Admin/js/todolist.js"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="../../Admin/js/data-table.js"></script>
  <!-- End custom js for this page-->
  


</body>


<!-- Mirrored from www.urbanui.com/melody/template/pages/samples/orders.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 15 Sep 2018 06:10:27 GMT -->
</html>
