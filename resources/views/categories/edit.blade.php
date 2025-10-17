@extends('layouts.admin')

@section('title', 'Edit Category | '.Session::get('site_name'))

@section('content')

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Update Category
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Category</li>
                </ol>
            </nav>
        </div>
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Category Edit Form</h4>
                        <form class="forms-sample" action="{{route('categories.update',$category->id)}}" method="post">
                            @csrf
                            @method('PUT')
                            @include('categories.category_form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection