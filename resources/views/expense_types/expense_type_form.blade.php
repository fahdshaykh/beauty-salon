
<div class="form-group">



    <label for="exampleInputUsername1">Expense Type</label>



    <input type="text" name="type" value="@if(isset($expense_type->type)){{$expense_type->type}}@else{{old('type')}}@endif" class="form-control @error('type') is-invalid @enderror" placeholder="Enter Expense Ttype" required/>

    @error('type')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>



<div class="form-group">



    <label for="exampleTextarea1">Discription</label>



    <textarea class="form-control" id="exampleTextarea1" name="description" rows="4">@if(isset($expense_type->description)){{$expense_type->description}}@else{{old('description')}}@endif</textarea>



</div>



<button type="submit" class="btn btn-primary mr-2">Submit</button>



<a class="btn btn-light" href="{{url('expense-types')}}">Cancel</a>