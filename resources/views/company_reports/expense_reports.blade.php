



        



        <?php $expense_report_array = array(5,6); ?>

        <?php $expense_report_link = implode(',', $expense_report_array); ?>

        <?php $total_amount = 0; ?>

        @foreach($expense_report as $expense)

        <?php $total_amount = $total_amount + $expense->amount; ?>

        @endforeach





        <div class="card">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <h4 class="card-title">Expense Report</h4>

                    </div>

                    <div class="col-md-6"></div>


                </div>

                <div class="row grid-margin">

                    <div class="col-12">

                      <div class="alert alert-warning mt-3" role="alert" style="text-align:center">

                          Start Date: <strong>@if($start_date == 'no_start_date') No Date Selected  @else {{$start_date}} @endif</strong>, End Date: <strong>@if($end_date == 'no_end_date') No Date Selected @else {{$end_date}} @endif</strong>

                      </div>

                      <div style="text-align:center">

                          <p>Grand Total: <i class="fas fa-dollar-sign"></i>{{$total_amount}}</p>

                      </div>

                    </div>

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th>Date </th>

                                        <th>Expense Type</th>

                                        <th>Company Name</th>

                                        <th>Amount</th>

                                        <th>Payment Method</th>

                                        <th>Payable</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    

                                    @foreach($expense_report as $expense)

                                    <tr>

                                        <td>{{$num}}</td>

                                        <td>{{$expense->type}}</td>

                                        <td>{{$expense->company_name}}</td>

                                        <td><i class="fas fa-dollar-sign"></i>{{$expense->amount}}</td>

                                        <td>{{$expense->method}}</td>

                                        <td>{{$expense->payable}}</td>

                                    </tr>

                                    <?php $num++; ?>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



<script>

function exportReports(_this) {

    let _url = $(_this).data('href');

    var path = {!! json_encode(url('/')) !!}

    window.location.href = path+_url;

}

</script>