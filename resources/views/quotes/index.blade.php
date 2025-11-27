@section('pagescript')



<link rel="stylesheet" href="{{asset('vendors/summernote/dist/summernote-bs4.css')}}">



<script src="{{asset('vendors/summernote/dist/summernote-bs4.min.js')}}"></script>



<script src="{{asset('js/editorDemo.js')}}"></script>



@stop







@extends('layouts.admin')







@section('title', 'Quotations | '. (auth()->user()->companySetting->site_name ?? ''))







@section('content')







<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                All quotations



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Quotation Listing</li>



                </ol>



            </nav>



        </div>







        <div class="card">



            <div class="card-body">



                <h4 class="card-title"></h4>



                <div class="row">



                    <div class="col-12">



                        <div class="table-responsive">



                            <table id="order-listing" class="table">



                                <thead>



                                    <tr class="bg-primary text-white">



                                        <th>Sr.No #</th>



                                        <th>E-mail</th>



                                        <th>Send Date</th>



                                        <th>Actions</th>



                                    </tr>



                                </thead>



                                <tbody>



                                    <?php $num = 1; ?>



                                    @forelse($quotations as $quote)



                                    <tr>



                                        <td>{{$num}}</td>



                                        <td>{{$quote->email}}</td>



                                        <td>{{date('m-d-Y', strtotime($quote->created_at))}}</td>



                                        <td>



                                            <a type="button" data-toggle="modal" onclick="quote_view('{{$quote->id}}')" data-target="#quote-view" data-quoteid="{{$quote->id}}">



                                                <i class="fa fa-eye text-primary"></i>



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



            </div>



        </div>



    </div>



<div class="modal fade" tabindex="-1" id="quote-modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">



  <div class="modal-dialog" role="document">



    <div class="modal-content">



      <div class="modal-header">



        <h5 class="modal-title" id="exampleModalLabel">Quote Detail</h5>



        <button type="button" class="close" data-dismiss="modal" aria-label="Close">



          <span aria-hidden="true">&times;</span>



        </button>



      </div>



      <div class="modal-body">



        <form>



          <div class="form-group">



            <label for="recipient-name" class="col-form-label">Recipient:</label>



            <input type="text" class="form-control" id="quote-email">



          </div>



          <div class="form-group">



            <textarea id="summernoteExample" name="message"></textarea>



          </div>



        </form>



      </div>



      <div class="modal-footer">



        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>



      </div>



    </div>



  </div>



</div>



@endsection







<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



<script>



function quote_view(id)



{



    console.log(id);



    $.ajax({



        url: "{{url('quote-view')}}/"+id,



        type: 'GET',



        data: {



            id: id



        },



        success: function(result) {



            $("#quote-modal").modal('show');



            $("#quote-email").val(result['0'].email);



            $("#summernoteExample").summernote("code", result['0'].message);



        }



    });







}







</script>