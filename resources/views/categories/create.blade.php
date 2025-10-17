@extends('layouts.admin')

@section('title', 'Create Category | '.Session::get('site_name'))

@section('content')

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Create Category
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Categories</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create Category</li>
                </ol>
            </nav>
        </div>
        @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            {{Session::get('error')}}
        </div>
        @endif
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Category Form</h4>
                        <form class="forms-sample" action="{{route('categories.store')}}" method="post">
                            @csrf
                            @include('categories.category_form')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection