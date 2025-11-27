
<div class="form-group">

    <label for="exampleInputUsername1">Category Name</label>

    <input type="text" name="name" value="@if(isset($category->name)){{$category->name}}@else{{old('name')}}@endif" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Category Name"/>
    @error('name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

<div class="form-group">

    <label for="exampleTextarea1">Discription</label>

    <textarea class="form-control" id="exampleTextarea1" name="description" rows="4">@if(isset($category->description)){{$category->description}}@else{{old('description')}}@endif</textarea>

</div>

<button type="submit" class="btn btn-primary mr-2">Submit</button>

<a class="btn btn-light" href="{{route('categories.index')}}">Cancel</a>