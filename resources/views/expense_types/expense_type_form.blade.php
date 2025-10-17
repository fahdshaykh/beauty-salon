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

<div class="form-group">

    <label for="exampleInputUsername1">Expense Type</label>

    <input type="text" name="type" value="@if(isset($expense_type->type)){{$expense_type->type}}@else{{old('type')}}@endif" class="form-control" placeholder="Enter Expense Ttype" required/>

</div>

<div class="form-group">

    <label for="exampleTextarea1">Discription</label>

    <textarea class="form-control" id="exampleTextarea1" name="description" rows="4">@if(isset($expense_type->description)){{$expense_type->description}}@else{{old('description')}}@endif</textarea>

</div>

<button type="submit" class="btn btn-primary mr-2">Submit</button>

<a class="btn btn-light" href="{{url('expense-types')}}">Cancel</a>