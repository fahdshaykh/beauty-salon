@section('pagescript')

<script src="{{asset('js/file-upload.js')}}"></script>

<script src="{{asset('js/typeahead.js')}}"></script>

<script src="{{asset('js/select2.js')}}"></script>

<script src="{{asset('js/formpickers.js')}}"></script>

@stop



@extends('layouts.admin')



@section('title', 'Edit Role | '.Session::get('site_name'))



@section('content')



<!-- partial -->

<div class="main-panel">

    <div class="content-wrapper">

        <div class="page-header">

            <h3 class="page-title">

                Edit Role

            </h3>

            <nav aria-label="breadcrumb">

                <ol class="breadcrumb">

                    <li class="breadcrumb-item"><a href="{{url('/roles')}}">Roles</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Edit Role</li>

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

            <div class="col-lg-12">

                <div class="card px-3">

                    <div class="card-body">

                      <form class="forms-sample" action="{{route('role.update',$role[0]->id)}}" method="post">

                        <h4 class="card-title">Add Role & Permissions</h4>

                        <div class="add-items d-flex">

                            <input type="text" name="role" value="{{$role[0]->role}}" class="form-control todo-list-input" placeholder="Enter Role Name">

                            <input type="submit" value="Update" class="add btn btn-primary">

                        </div>

                        <div class="col-lg-12 grid-margin stretch-card">

                            <div class="card">

                                <div class="card-body">

                                    <div class="row">

                                      <div class="col-md-3">

                                        <h4 class="card-title">Set Permissions</h4>

                                      </div>

                                      <div class="col-md-6"></div>

                                        <div class="col-md-3">

                                              <div class="form-check form-check-primary">

                                                <label class="form-check-label">

                                                    <input type="checkbox" onclick="toggle(this);" value="1" class="form-check-input">

                                                    Check All

                                                </label>

                                              </div>

                                        </div>

                                    </div>

                                    <div class="table-responsive">

                                    <table class="table table-bordered">

                                            <thead>

                                                <tr>

                                                    <th>Modules</th>

                                                    <th>Read</th>

                                                    <th>Create</th>

                                                    <th>Edit</th>

                                                    <th>Delete</th>

                                                    <th>View</th>

                                                    <th>Export</th>

                                                    <th>Graph</th>

                                                </tr>

                                            </thead>

                                            <tbody>

                                                

                                                @foreach($permissions as $permission)

                                                <tr>

                                                    <td class="py-1">

                                                        <div class="form-check form-check-primary">

                                                            {{$permission[0]->name}}

                                                        </div>

                                                    </td>

                                                    <?php $access_level = 1; ?>

                                                    @foreach($permission as $perm)

                                                    <td>

                                                        <div class="form-check form-check-primary">

                                                            <label class="form-check-label">

                                                                <input type="checkbox" name="access_id[{{$perm->permission_id}}][]" value="{{$access_level}}" class="form-check-input"

                                                                @foreach($new_array["permissions"] as $key => $value) @if($key == $perm->permission_id) @foreach($value as $val) @if($val == $access_level) checked @endif  @endforeach @endif @endforeach>

                                                            </label>

                                                        </div>

                                                        <?php if($perm->permission_id==5 && $access_level==1)

                                                            { echo "Booking Report";  }

                                                            elseif($perm->permission_id==5 && $access_level==2) { echo "Export BR"; }

                                                            elseif($perm->permission_id==5 && $access_level==3){ echo "Customer report"; }

                                                            elseif($perm->permission_id==5 && $access_level==4){ echo "Export CR"; }

                                                            elseif($perm->permission_id==5 && $access_level==5){ echo "Expense Report"; }

                                                            elseif($perm->permission_id==5 && $access_level==6){ echo "Export ER"; }

                                                            elseif($perm->permission_id==5 && $access_level==7){ echo "Graph Report"; }

                                                            elseif($perm->permission_id==4 && $access_level==5){ echo "View Invoice"; }

                                                            elseif($perm->permission_id==3 && $access_level==5){ echo "Export Customer"; }

                                                            elseif($perm->permission_id==16 && $access_level==2){ echo "Send Message"; }

                                                            elseif($perm->permission_id==16 && $access_level==3){ echo "Send PostCard"; }

                                                            elseif($perm->permission_id==16 && $access_level==4){ echo "View PostCard History"; }

                                                            elseif($perm->permission_id==17 && $access_level==2){ echo "General Settings"; }

                                                            elseif($perm->permission_id==17 && $access_level==3){ echo "Review SMS"; }

                                                            elseif($perm->permission_id==17 && $access_level==4){ echo "Email Settings"; }

                                                            elseif($perm->permission_id==17 && $access_level==5){ echo "Tax Settings"; }

                                                            elseif($perm->permission_id==17 && $access_level==6){ echo "PostCard Settings"; }

                                                             ?>

                                                    </td>

                                                    <?php $access_level++ ?>

                                                    @endforeach

                                                </tr>

                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                      @csrf

                      </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

function toggle(source) {

  var checkboxes = document.querySelectorAll('input[type="checkbox"]');

  for (var i = 0; i < checkboxes.length; i++) {

      if (checkboxes[i] != source)

      checkboxes[i].checked = source.checked;

  }

}

</script>

@endsection