@section('pagescript')

    <script src="{{asset('js/file-upload.js')}}"></script>

    <script src="{{asset('js/typeahead.js')}}"></script>

    <script src="{{asset('js/select2.js')}}"></script>

    <script src="{{asset('js/formpickers.js')}}"></script>

@stop



@extends('layouts.admin')



@section('title', 'Edit Service | '.Session::get('site_name'))


<style>
/* reusable pill‑style radios */
.radio-toggle{display:none;}                              /* hide native input   */
.radio-pill{
    display:inline-block;
    padding:.45rem 1.25rem;
    margin:0 .35rem .5rem 0;
    border:2px solid #ced4da;
    border-radius:.5rem;
    font-weight:500;
    cursor:pointer;
    transition:all .15s ease-in-out;
}
.radio-toggle:checked + .radio-pill{
    background:#0d6efd;                                   /* Bootstrap “primary” */
    color:#fff;
    border-color:#0d6efd;
    box-shadow:0 0 0 .25rem rgba(13,110,253,.25);
}
.radio-pill:hover{border-color:#0d6efd;}
</style>

@section('content')





 <!-- partial -->

 <div class="main-panel">        

        <div class="content-wrapper">

          <div class="page-header">

            <h3 class="page-title">

                Update Services

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                <li class="breadcrumb-item"><a href="{{route('services.index')}}">Services</a></li>

                <li class="breadcrumb-item active" aria-current="page">Edit Service</li>

                </ol>

            </nav>

          </div>

          @if ($errors->any())

              <div class="alert alert-danger alert-dismissible">

                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

                  <ul>

                      @foreach ($errors->all() as $error)

                          <li>{{ $error }}</li>

                      @endforeach

                  </ul>

              </div>

          @endif

          <div class="row">

            <div class="col-md-6 grid-margin stretch-card">

              <div class="card">

                <div class="card-body">

                  <h4 class="card-title">Update Service</h4>

                  <form class="forms-sample" action="{{route('services.update',$services->id)}}" method="post">

                  @csrf

                  @method('PUT')

                  <div class="form-group">

                      <label>Select Category</label>

                      <select id="category_id" name="category_id" class="js-example-basic-single w-100">

                        @foreach($categories as $category)

                            <option value="{{$category->id}}" @if($services->category_id == $category->id) selected @endif >{{$category->name}}</option>

                        @endforeach

                      </select>

                    </div>

                    <div class="form-group">

                      <label for="exampleInputUsername1">Service Name:</label>

                      <input type="text" class="form-control" value="{{ $services->name }}" name="name" id="exampleInputUsername1" placeholder="Enter Service Name" required>

                    </div>

                    <div class="form-group">

                    <label for="employees">Assign Employees</label>

                    <select name="employees[]" class="form-control js-example-basic-multiple w-100" multiple="multiple" required>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" 
                            {{ in_array($employee->id, $services->employees->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{ $employee->name }}
                        </option>
                    @endforeach
                    </select>

                    </div>

                    <div class="form-group">
                      <label class="d-block mb-2 font-weight-bold">Service Type</label>

                      <input type="radio" name="risk_status" value="not_risky" id="risk_not"
                            class="radio-toggle" {{ $services->risk_status=='not_risky'?'checked':'' }}>
                      <label for="risk_not" class="radio-pill">Not Risky</label>

                      <input type="radio" name="risk_status" value="risky" id="risk_yes"
                            class="radio-toggle" {{ $services->risk_status=='risky'?'checked':'' }}>
                      <label for="risk_yes" class="radio-pill">Risky</label>
                  </div>

                  <!-- Price Type -->
                  <div class="form-group">
                      <label class="d-block mb-2 font-weight-bold">Price Type</label>

                      <input type="radio" name="price_type" value="fixed" id="price_fixed"
                            class="radio-toggle" {{ $services->price_type=='fixed'?'checked':'' }}>
                      <label for="price_fixed" class="radio-pill">Fixed</label>

                      <input type="radio" name="price_type" value="modify" id="price_modify"
                            class="radio-toggle" {{ $services->price_type=='modify'?'checked':'' }}>
                      <label for="price_modify" class="radio-pill">Modify Able</label>
                  </div>

                    <div class="form-group">

                      <label for="exampleInputUsername1">Service Price:</label>

                      <div class="input-group">

                        <div class="input-group-prepend">

                          <span class="input-group-text bg-primary text-white"><i class="fas fa-dollar-sign"></i></span>

                        </div>

                        <input type="text"  name="price" value="{{ $services->price }}" class="form-control" aria-label="Amount (to the nearest dollar)">

                      </div>

                    </div>

                    <div class="form-group">

                      <div class="col" style="margin-left: -8px;">

                          <input type='text' class="color-picker" name="service_color" value="{{ $services->service_color }}" />

                      </div>

                    </div>

                    <div class="form-group">

                        <div class="form-group mt-2">

                          <label for="exampleInputUsername1">Description</label>

                          <textarea class="form-control" id="exampleTextarea1" name="description" rows="4" maxlength="160"> {{ $services->description }} </textarea>

                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary mr-2">Submit</button>

                    <a class="btn btn-light" href="{{route('services.index')}}">Cancel</a>

                  </form>

                </div>

              </div>

            </div>

          </div>

        </div>
      

@endsection



