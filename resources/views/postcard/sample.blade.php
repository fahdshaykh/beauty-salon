@extends('layouts.admin')



@section('title', 'Send PostCard | '.Session::get('site_name'))



@section('content')
    <div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              PostCard Samples
            </h3>
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#">Postcard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Samples</li>
              </ol>
            </nav>
          </div>
          <div class="row grid-margin">
            <div class="col-lg-12">
              <div class="card">
                <div class="card-body">
                <p class="card-text">
                    Click on any sample to download & edit
                  </p>
                  <div id="lightgallery" class="row lightGallery">
                    <a href="{{asset('postcardsamples/eastern-postcard-1.pdf')}}" class="image-tile"><img src="{{asset('postcardsamples/1_a.png')}}" alt="image small"></a>
                    <a href="{{asset('postcardsamples/eastern-postcard-2.pdf')}}" class="image-tile"><img src="{{asset('postcardsamples/2_a.png')}}" alt="image small"></a>
                    <a href="{{asset('postcardsamples/eastern-postcard-3.pdfg')}}" class="image-tile"><img src="{{asset('postcardsamples/3_a.png')}}" alt="image small"></a>
                    <a href="{{asset('postcardsamples/eastern-postcard-4.pdf')}}" class="image-tile"><img src="{{asset('postcardsamples/4_a.png')}}" alt="image small"></a>
                    <a href="{{asset('postcardsamples/eastern-postcard-5.pdf')}}" class="image-tile"><img src="{{asset('postcardsamples/5_a.png')}}" alt="image small"></a>
                    <a href="{{asset('postcardsamples/eastern-postcard-6.pdf')}}" class="image-tile"><img src="{{asset('postcardsamples/6_a.png')}}" alt="image small"></a>
                    <a href="{{asset('postcardsamples/eastern-postcard-7.pdf')}}" class="image-tile"><img src="{{asset('postcardsamples/7_a.png')}}" alt="image small"></a>
                    <a href="{{asset('postcardsamples/eastern-postcard-1.pdf')}}" class="image-tile"><img src="{{asset('postcardsamples/1_a.png')}}" alt="image small"></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        
        </div>
    </div>
@endsection