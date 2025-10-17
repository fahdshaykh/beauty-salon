@extends('layouts.admin')



@section('title', 'Send PostCard | '.Session::get('site_name'))



@section('content')
<div class="main-panel">          
    <div class="content-wrapper">
        <div class="page-header">
        <h3 class="page-title">
        Postcard template
        </h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{url('postcard')}}">PostCard</a></li>
            <li class="breadcrumb-item active" aria-current="page">PostCard Template</li>
            </ol>
        </nav>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="faq-section">
                            <div id="accordion-1" class="accordion">
                                <div class="card">
                                    <div class="card-header" id="headingOne">
                                        <h5 class="mb-0">
                                        <a data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Postcard template
                                        </a>
                                        </h5>
                                    </div>
                                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion-1">
                                        <div class="card-body">
                                        A postcard is 2 sided (front and back), gloss and 300 gsm. The back is automatically stamped with the recipient address.

                                        If you only supply 1 page, we will put this on the front. The back will be stamped with the address..
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingTwo">
                                        <h5 class="mb-0">
                                        <a data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        File formats
                                        </a>
                                        </h5>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion-1">
                                        <div class="card-body">
                                        We support the following formats:
                                            <ul>
                                            <li>PDF (recommended)</li>
                                            <li>PNG</li>
                                            <li>JPG</li>
                                            <li> JPEG</li>
                                            <li>BMP</li>
                                            <li> TIFF</li>
                                        </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingThree">
                                        <h5 class="mb-0">
                                        <a data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            Allowed Areas Template
                                        </a>
                                        </h5>
                                    </div>
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion-1">
                                        <div class="card-body">
                                        We automatically stamp the recipient address onto the postcard. It's important that you don't have content in that area or we will stamp over it. You can download our example PDF template here:
                                        <a target="_blank" href="https://files.helpdocs.io/4abz6eqtwz/articles/ysyql7bsr1/1549438238575/a-5-postcard-template.pdf">Download Postcard Template</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingFour">
                                        <h5 class="mb-0">
                                        <a data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                            Artwork size
                                        </a>
                                        </h5>
                                    </div>
                                    <div id="collapseFour" class="collapse" aria-labelledby="collapseFour" data-parent="#accordion-1">
                                        <div class="card-body">
                                        The postcard size should be A5 (210 mm x 148 mm or 8.3 x 5.8 inches) with a 5 mm bleed and no border.

                                        If you're using an image the size should be:
                                        <ul>
                                            <li>w: 2480 px</li>
                                            <li>h: 1748 px</li>
                                            <li>density: 300 dpi</li>

                                        </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header" id="headingFive">
                                        <h5 class="mb-0">
                                        <a data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        PDF Specifications
                                        </a>
                                        </h5>
                                    </div>
                                    <div id="collapseFive" class="collapse" aria-labelledby="collapseFive" data-parent="#accordion-1">
                                        <div class="card-body">
                                        If you choose to use PDF format, you will need to make sure:.
                                        <ul>
                                            <li>All your fonts are embedded.</li>
                                            <li>The PDF is PDF/A compliant.</li>
                                            <li>The PDF should be flattened.</li>
                                            <li>The file should be in A5 paper size (210 mm x 148 mm or 8.3 x 5.8 inches) with a 5 mm bleed and no border.</li>
                                            <li>All pages should be in landscape orientation.</li>
                                            <li>There should be 2 pages. The first page is the front. The second page is the back. If you only supply 1 page, we will put this on the front. The back will be stamped with the address.</li>
                                            Failure to adhere to these specifications may result in the letter being cancelled or charged at a higher rate for us to adjust the document.
                                        </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>
@endsection