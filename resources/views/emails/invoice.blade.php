<!DOCTYPE html>

<html lang="en">

    <head>

        <meta charset="UTF-8" />

        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>mail</title>

    </head>

    <?php



$invoice = $detail_array['invoice'];

$due_date = $detail_array['due_date'];

$price = $detail_array['total_price'];

$name = $detail_array['first_name'].$detail_array['last_name'];

$phone = $detail_array['primary_phone'];

$email = $detail_array['email'];

$address = $detail_array['address'];

$comment = $detail_array['comment'];

$payment_method = $detail_array['payment_method'];

$chequenumber = $detail_array['chequenumber'];

$postcode = $detail_array['postcode'];

$payment_days = $detail_array['payment_days'];

$vat = $detail_array['vat'];

$header = $detail_array['header'];

$footer = $detail_array['footer'];

$company_logo = $detail_array['company_logo'];

$services = $detail_array['services'];

?>

    <body style="margin: 0; padding: 0; text-align: center;">

        <table id="table_1" style="text-indent: 20px; width: 800px; border-collapse: collapse; border-spacing: 0; margin-left: auto; margin-right: auto; text-align: left;">

            <tr class="row_1">

                <td colspan="2"><hr style="height: 10px; background-color: #392c70;" /></td>

            </tr>

            <tr class="row_2" style="padding: 10px;">

                <td style="text-indent: 20px; width: 50%;"><h3>Invoice</h3></td>

                <td>

                    <img src="@if($company_logo){{asset('email_logo/'.$company_logo)}} @else {{asset('profiles/power_diary.png')}} @endif" width="250" height="50" alt="logo" />

                </td>

            </tr>

            <tr>

                <td>

                    <br />

                    <br />

                    <br />

                    <br />

                    <br />

                </td>

            </tr>

            <tr>

                <td> <b> For </b></td>

                <td>

                   <b> From </b>

                </td>

            </tr>

            <tr>

                <td>

                    <div><span>CLIENT : </span> {{$name}}</div>

                    <div><span>ADDRESS : </span> {{$address}}</div>

                    <div><span>EMAIL : </span> <a href="mailto:{{$email}}">{{$email}}</a></div>

                    <div><span>DATE : </span> {{date("M-d-Y")}}</div>

                    <div><span>DUE DATE : </span>{{date("M-d-Y", strtotime($due_date))}}</div>

                </td>

                <td style="text-indent: 0%;">

                    <p>{!! $header !!}</p>

                    <p>Payment Method: {{ucfirst(trans($payment_method))}}</p>

                    @if($payment_method=='cheque')

                    <br />

                    Cheque#: {{$chequenumber}}. @endif

                    <span>Payment Days: {{$payment_days}}</span>

                    

                </td>

            </tr>



            <tr style="height: 20px; border-bottom: 1px solid #392c70;"></tr>

        </table>

        <br />

        <table id="table_2" style="width: 780px; text-align: left !important; margin-left: auto; margin-right: auto;">

            <tr style="line-height: 25px;">

                <td style="text-align: left; ">

                    Invoice #:

                </td>

                <td>

                    {{$invoice}}-{{$postcode}}

                </td>

            </tr>

            <tr style="line-height: 25px;">

                <td style="text-align: left; width: 100px;">

                    Date :

                </td>

                <td>

                    {{date("M-d-Y")}}

                </td>

            </tr>



            <tr style="line-height: 25px;">

                <td style="text-align: left; width: 100px;">

                    Due :

                </td>

                <td>

                    {{date('d-m-Y', strtotime($due_date))}}

                </td>

            </tr>

            <tr style="height: 20px; border-bottom: 1px solid #392c70;"></tr>

        </table>

        <table id="table_3" style="width: 800px; border-collapse: collapse; border-spacing: 0; text-align: left; padding: 5px; margin-left: auto; margin-right: auto;">

            <tr class="row_1" style="background-color: #392c70; line-height: 35px; color: #fff;">

                <th style="width: 50%;">&nbsp; Services</th>

                <th style="width: 15%; text-align: center;">Qty</th>

                <th style="width: 15%; text-align: center;">Price</th>

                <th style="width: 20%; text-align: center;"></th>

            </tr>

            <?php $subtotal = 0;?>

            @foreach($services as $service)

            <tr style="line-height: 30px;">

                <td style="text-indent: 10px;">{{$service['service_name']}}</td>

                <td style="text-align: center;">1</td>

                <td style="text-align: center;"> @if($service['service_price']>0) {{$service['service_price']}} @endif</td>

            </tr>

            <?php $subtotal += $service['service_price'];?>

            @endforeach

            

            <tr style="height: 20px; border-bottom: 1px solid #392c70;">

                <td></td>

            </tr>



            <tr style="line-height: 50px;">

                <td></td>

                <td style="text-align: center;"><b> Subtotal : </b></td>

            

                <td style="text-align: center;">£{{$price}}</td>

                <td></td>

            </tr>

            @if($vat>0)

            <tr style="line-height: 50px;">

                <td></td>

                

                <td style="text-align: center;"><b> VAT : </b></td>

                <td style="text-align: center;">{{$vat}}</td>

                

                <td></td>

            </tr>

            @endif

            <tr style="height: 20px; border-bottom: 1px solid #392c70;"></tr>

            <tr style="line-height: 50px;">

                <td></td>

                <td style="text-align:center;" > <h3> Grand Total :</h3></td>

                <td style="text-align: center"> <b> £{{$price+$vat}} </b></td>

                <td></td>

            </tr>

            <!-- <tr>

                <td></td>

                <td style="text-align: center;"><h3>Balance Due</h3></td>

                <td style="text-align: center;">$220.00</td>

                <td></td>

            </tr> -->

            <tr style="height: 20px; border-bottom: 1px solid #392c70;"></tr>

        </table>

        <table style="width: 800px; border-collapse: collapse; border-spacing: 0; text-align: left; padding: 5px; margin-left: auto; margin-right: auto;">

        <tr>

                <td>

                {{$comment}}

                </td>

            </tr>

            <tr>

                <td>

                    {!! $footer !!}

                </td>

            </tr>

            <!-- <tr style="  line-height: 50px;" style="text-indent: 20px;">

            <td> <b> Notes</b></td>

        </tr>

        <tr style="line-height:20px;text-indent: 20px;">

            <td>{{$comment}}</td>

        </tr>

        <tr style="line-height:20px;text-indent: 20px;">

            <td>Thank You For Your Business!</td>

        </tr> -->

            <tr aria-rowspan="5" style="line-height: 50px;"></tr>

        </table>

    </body>

</html>

