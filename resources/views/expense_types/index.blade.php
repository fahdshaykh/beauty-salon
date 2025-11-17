@extends('layouts.admin')



@section('title', 'Expense Types | '.Session::get('site_name'))



@section('content')

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                All Expense Types

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('expense-types')}}">Expense Types</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Expense Type Listing</li>

                </ol>

            </nav>

        </div>



        <div class="alert alert-success" style="display:none;" id="status-success"></div>



        @if(Session::has('success'))

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('success')}}

        </div>



        @elseif(Session::has('destroy'))

        <div class="alert alert-danger alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('destroy')}}

        </div>

        @endif



        @if(Session::has('update'))

        <div class="alert alert-success alert-dismissible">

            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>

            {{Session::get('update')}}

        </div>

        @endif





        <div class="col-lg-12 grid-margin stretch-card">

      

              <div class="card">

              <!-- <a class="btn btn-primary btn-fw {{ Request::is('expenses*') ? 'active' : '' }}" href="{{url('expense-types/creat')}}"> Add Expense Type</a> -->

                <div class="card-body">

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

                  @can('create expense types')
                    <h4 class="card-title" style="float:right;"><a class="btn btn-primary btn-fw" href="{{url('expense-types/create')}}"> Add Expense Type</a></h4>
                  @endcan

                  <div class="table-responsive">

                    <table class="table table-hover">

                      <thead>

                        <tr>

                          <th>Sr No#</th>

                          <th>Type</th>

                          <th>Status</th>

                          <th>Action</th>

                        </tr>

                      </thead>

                      <tbody>

                      <?php $num = 1; ?>

                    

                        @forelse($expense_types as $expense_type)

                        <tr>

                            <td>{{$num}}</td>

                            <td>{{$expense_type->type}}</td>

                            <td>
                                <label class="switch">
                                    <input type="checkbox"
                                        id="expensetype-{{ $expense_type->id }}"
                                        data-name="{{ $expense_type->type }}"
                                        onclick="changeExpenseTypeStatus(this, {{ $expense_type->id }})"
                                        {{ $expense_type->is_active ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </td>


                            <td>
                                <div class="d-flex gap-1">
                                    @can('edit expense types')
                                    <a href="{{url('expense-types/edit',$expense_type->id)}}"
                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    @endcan
                                </div>
                            </td>


                        </tr>

                        @empty

                        <tr><td colspan='3'> No Data Available</td></tr>

                        <?php $num++; ?>

                        @endforelse

                 

                      </tbody>

                    </table>

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
            window.location = "{{ url('/expense-types') }}?rows=" + this.value;
        });
    }
});
</script>

<script>
function changeExpenseTypeStatus(_this, id) {
    var $checkbox = $(_this);
    var status    = $checkbox.prop('checked') ? 1 : 0;
    var typeName  = $checkbox.data('name'); // Get expense type name

    $.ajax({
        url: "{{ url('expense-type-status') }}",
        type: 'GET',
        data: { id: id, status: status },
        success: function (result) {
            iziToast.success({
                title: typeName + (status ? ' Activated!' : ' Deactivated!'),
                message: result.success || 'Status updated successfully.',
                position: 'topRight',
                icon: status ? 'fas fa-check-circle' : 'fas fa-ban',
                timeout: 5000
            });
        },
        error: function () {
            // Revert checkbox on error
            $checkbox.prop('checked', !status);

            iziToast.error({
                title: typeName + ' – Update Failed!',
                message: 'Could not update status. Please try again.',
                position: 'topRight',
                icon: 'fas fa-exclamation-triangle',
                timeout: 6000
            });
        }
    });
}
</script>