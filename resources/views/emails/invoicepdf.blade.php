<!DOCTYPE html>

<html lang="en">

  <head>

    <meta charset="utf-8">

    <title>Invoice</title>

    <link rel="stylesheet" href="{{asset('css/invoice.css')}}" media="all" />

  </head>

  <body>

<?php

$invoice = $detail_array['invoice'];

$due_date = $detail_array['due_date'];

$price = $detail_array['total_price'];

$name = $detail_array['first_name'].$detail_array['last_name'];

$phone = $detail_array['primary_phone'];

$email = $detail_array['email'];

$address = $detail_array['address'];

$comment = $detail_array['comment'];

$payment_days = $detail_array['payment_days'];

$vat = $detail_array['vat'];

$payment_instrument = $detail_array['payment_instrument'];

$chequenumber = $detail_array['chequenumber'];

$postcode = $detail_array['postcode'];

$header = $detail_array['header'];

$footer = $detail_array['footer'];

$company_logo = $detail_array['company_logo'];

$services = $detail_array['services'];

?>

    <header class="clearfix">

      <div id="logo">
      @if(!empty($detail_array['company_logo_base64']))
        <img src="{{ $detail_array['company_logo_base64'] }}" width="250" height="50" alt="logo"/>
      @elseif(!empty($detail_array['company_logo']))
        <img src="{{ asset('email_logo/'.$detail_array['company_logo']) }}" width="250" height="50" alt="logo"/>
      @else
        <img src="{{ asset('profiles/power_diary.png') }}" width="250" height="50" alt="logo"/>
      @endif
      </div>

      <h1>INVOICE# {{$invoice}}-{{$postcode}}</h1>

     

        <div id="company" class="clearfix">

          <div>{!! $header !!}</div>

                

        </div>

        <div id="project">

          <div><span>CLIENT</span> {{$name}}</div>

          <div><span>ADDRESS</span> {{$address}}</div>

          <div><span>EMAIL</span> <a href="mailto:{{$email}}">{{$email}}</a></div>

          <div><span>DATE</span> {{date("M-d-Y")}}</div>

          <div><span>DUE DATE</span>{{date("M-d-Y", strtotime($due_date))}}</div>

        </div>

      

    </header>

    <main>

      <table>

        <thead>

          <tr>

            <th class="service">SERVICE</th>

            <th colspan="2">QTY</th>

            <th></th>

            <th colspan="2">PRICE</th>

          </tr>

        </thead>

        <tbody >

      <?php $subtotal = 0;?>

        @foreach($services as $service)

          <tr>

            <td>

          {{$service['service_name']}}

            </td>

            <td colspan="2">

              1

            </td>

            <td>

            </td>

            <td colspan="2">

           

            @if($service['service_price']>0) £{{$service['service_price']}} @endif

            </td>

          </tr>

                      @endforeach

          <tr>

            <td colspan="3"></td>

            <td> <b> SUBTOTAL </b></td>

            <td class="total">£{{$price}}</td>

          </tr>

          @if($vat>0)

          <tr>

            <td  colspan="3"></td>

            <td>VAT</td>

            <td class="total">£{{$vat}}</td>

          </tr>

          @endif

          <tr>

            <td class="grand total" colspan="3"></td>

              <td  class="grand total">GRAND TOTAL</td>

            <td class="grand total">£{{$price+$vat}}</td>

          </tr>

        </tbody>

      </table>

      <div id="notices">

        <div>Notes:</div>

        <div class="notice">{{$comment}}</div>

      </div>

    </main>

    <footer>

    {!! $footer !!}

    </footer>

  </body>

</html>