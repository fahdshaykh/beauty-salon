@extends('layouts.admin')







@section('title', 'Edit Customer | '. (auth()->user()->companySetting->site_name ?? ''))







@section('content')







<!-- partial -->



<div class="main-panel">



    <div class="content-wrapper">



        <div class="page-header">



            <h3 class="page-title">



                Update Customer



            </h3>



            <nav aria-label="breadcrumb">



                <ol class="breadcrumb">



                    <li class="breadcrumb-item"><a href="{{route('customers.index')}}">Customers</a></li>



                    <li class="breadcrumb-item active" aria-current="page">Edit Customer</li>



                </ol>



            </nav>



        </div>



        <div class="row">



            <div class="col-12 grid-margin">



                <div class="card">



                    <div class="card-body">



                        <h4 class="card-title">Customer Edit Form</h4>



                        <form class="forms-sample" action="{{route('customers.update',$customer->id)}}" method="post">



                            @csrf



                            @method('PUT')



                            @include('customers.customer_form')



                        </form>



                    </div>



                </div>



            </div>



        </div>



    </div>



</div>

<script>

$('#lookup_field').setupPostcodeLookup({

    api_key: 'ak_kr1v46iv0aNNht6IWqYTiNYN2lxLb',

    output_fields: {

        line_1: '#first_line',

        line_2: '#second_line',

        line_3: '#third_line',

        post_town: '#post_town',

        postcode: '#postcode'

    }

});

function updateAddress(elem){

    var optionsText = elem.options[elem.selectedIndex].text;



    $("#exampleTextarea1").val(optionsText);

    const myArr = elem.value.split(",");

    $('#latitude').val(myArr[0]);

    $('#longitude').val(myArr[1]);

}

</script>

@endsection