

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

                    </div>

                    <div class="col-12">

                        <div class="table-responsive">

                            <table id="order-listing" class="table">

                                <thead>

                                    <tr class="bg-primary text-white">

                                        <th>Serial </th>

                                        <th>Inventory Name</th>
                                        <th>Remaining Quantity</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $num = 1; ?>

                                    

                                    @foreach($inventory_report as $remain)

                                    <tr>

                                        <td>{{$num}}</td>

                                        <td>{{ $remain->inventory->name ?? '-' }}</td>
                                        <td>{{ $remain->total_quantity }}</td>

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