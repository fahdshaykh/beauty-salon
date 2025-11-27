

@section('pagescript')

<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

@stop

@extends('layouts.admin')



@section('title', 'Add Holidays | '. (auth()->user()->companySetting->site_name ?? ''))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Add Holidays

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="#">Holidays</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Add Holidays</li>

                </ol>

            </nav>

        </div>

        <div class="row">

          <div class="col-md-12 grid-margin stretch-card">

            <div class="card">

              <div class="card-body">

                <h4 class="card-title">Holidays Setup</h4>

                <p class="card-description">

                  Select <code>Starting date</code> & <code>Ending Date.</code> to apply holidays in calendar!

                </p>

                <form class="form-inline" action="{{url('update-holidays')}}" method="post" id="expense-filters">
                @csrf
                  <div class="input-group">

                    <div id="datepicker-popup-new" class="input-group date datepicker">

                      <input type="text" name="start_date" class="form-control">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  <div class="input-group">

                    <div id="datepicker-popup-new2" class="input-group date datepicker">

                      <input type="text" name="end_date" class="form-control">

                      <span class="input-group-addon input-group-append border-left">

                        <span class="far fa-calendar input-group-text"></span>

                      </span>

                    </div>

                  </div>

                  

                   

                   

                    <button type="submit" value="submit" class="btn btn-primary mb-2">Update</button>


                </form>

              </div>

            </div>

          </div>

        </div>
        <div class="card" id="report">
        <div class="card">
            <div class="card-body">
              <!-- Pagination Dropdown -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="pagination-dropdown">
                            <form class="form-inline">
                                <div class="form-group">
                                    <label for="rows">Records:&nbsp;</label>
                                    <select class="form-control" id="rows" name="rows">
                                        <option value="5" @if($rows == 5) selected @endif > 5 </option>
                                        <option value="10" @if($rows == 10) selected @endif > 10 </option>
                                        <option value="20" @if($rows == 20) selected @endif > 20 </option>
                                        <option value="50" @if($rows == 50) selected @endif > 50 </option>
                                        <option value="100" @if($rows == 100) selected @endif > 100 </option>
										                    <option value="500" @if($rows == 500) selected @endif > 500 </option>                        
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                  <div class="col-12">
                      <div class="table-responsive">
                          <table class="table table-striped">
                              <thead>
                                  <tr class="bg-primary text-white">
                                      <th>Sr</th>
                                      <th>Start Date</th>
                                      <th>End Date</th>
                                      <th>Date Range</th>
                                      <th>Actions</th>
                                  </tr>
                              </thead>

                              <tbody>
                                  <?php $num = 1; ?>
                                  @forelse($holidays as $holiday)
                                  <tr>
                                      <td>{{ $num }}</td>
                                      <td>{{ $holiday->start_date }}</td>
                                      <td>{{ $holiday->end_date }}</td>
                                      <td>{{ $holiday->days }}</td>

                                      <td>
                                          <a onclick="return confirm('Are you sure to delete?');" 
                                            href="{{ url('delete-holidays', $holiday->id) }}">
                                              <i class="fas fa-trash text-danger"></i>
                                          </a>
                                      </td>
                                  </tr>
                                  <?php $num++; ?>
                                   @empty

                                    <tr style="text-align: center">
                                        <td colspan='6'> No Data Available</td>
                                    </tr>

                                    @endforelse
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>


                <!-- Pagination Links -->
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>Showing {{ $holidays->firstItem() }} to {{ $holidays->lastItem() }} of {{ $holidays->total() }} entries</p>
                        {{ $holidays->links() }}
                    </div>
                </div>

            </div>
        </div>
      </div>
    </div>

</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsSelect = document.getElementById('rows');

    if (rowsSelect) {
        rowsSelect.addEventListener('change', function () {
            window.location = "{{ url('/add-holidays') }}?rows=" + this.value;
        });
    }
});
</script>