

@if($booking_detail != '')
    @foreach($booking_detail as $book)
        <?php
            $booking_id = $book->id;
            $customer_id = $book->customer_id;
            $user_id = $book->user_id;
            $due_date = $book->due_date;
            $due_time = $book->due_time;
            $service_time = $book->service_time;
            $total_price = $book->total_price;
            $comment = $book->comment;
            $pay_days = $book->payment_days;

            $is_home_service = $book->is_home_service;
            $booking_address = $book->booking_address;
        ?>
    @endforeach
    <input type="hidden" name="booking_id" value="{{$booking_id}}">
@endif



<div class="row">
    <div class="col-12 col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <!-- Home Service Toggle & Address Box -->
                <div class="card border mb-3" style="border-radius: 8px; background-color: #f8f9fa;">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="mr-3 text-primary">
                                    <i class="fas fa-home fa-2x"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-dark">Home Service</h6>
                                    <small class="text-muted">Enable if this appointment is performed at the client's location</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="switch mb-0">
                                    <input type="checkbox" id="is_home_service" name="is_home_service" value="1"
                                        @if((isset($is_home_service) && $is_home_service) || (is_array($booking_detail) && isset($booking_detail[0]) && $booking_detail[0]->is_home_service)) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <!-- Address Field -->
                        <div class="mt-3 pt-3 border-top" id="address_field" style="@if((isset($is_home_service) && $is_home_service) || (is_array($booking_detail) && isset($booking_detail[0]) && $booking_detail[0]->is_home_service)) display: block; @else display: none; @endif">
                            <label class="font-weight-bold">Service Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="booking_address" id="booking_address"
                                    class="form-control @error('booking_address') is-invalid @enderror"
                                    placeholder="Start typing or enter service address"
                                    autocomplete="off"
                                    value="@if(isset($booking_address)){{ $booking_address }}@else{{ old('booking_address') }}@endif"
                                    @if((isset($is_home_service) && $is_home_service) || (is_array($booking_detail) && isset($booking_detail[0]) && $booking_detail[0]->is_home_service)) required @endif>
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-map-marker-alt text-danger"></i>
                                    </span>
                                </div>
                            </div>
                            @error('booking_address')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="mb-0">Select Customer</label>
                        <a href="javascript:void(0)" id="btnViewCustomerHistory" class="text-primary small font-weight-bold" style="display:none;" title="View Booking History">
                            <i class="fas fa-history mr-1"></i>View History
                        </a>
                    </div>
                    <select id="customer_id" name="customer_id" class="form-control @error('customer_id') is-invalid @enderror js-example-basic-single w-100" style="width: 100% !important;" required>
                        @if($booking_detail == '')
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{$customer->id}}" data-show-history="{{ $customer->show_booking_history ?? 1 }}">{{$customer->first_name}} {{$customer->last_name}} ({{$customer->primary_phone}})</option>
                            @endforeach
                        @else
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{$customer->id}}" data-show-history="{{ $customer->show_booking_history ?? 1 }}" @if($customer_id==$customer->id) selected @endif >{{$customer->first_name}} {{$customer->last_name}}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('customer_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Select Employee</label>
                    <select id="user_id" name="user_id" class="form-control @error('user_id') is-invalid @enderror js-example-basic-single w-100" style="width: 100% !important;" required>
                        @if($booking_detail == '')
                            <option value="">Select employee</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        @else
                            <option value="">Select employee</option>
                            @foreach($users as $user)
                                <option value="{{$user->id}}" @if($user_id==$user->id) selected @endif >{{$user->name}}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('user_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <input type="hidden" id="selected_services" value="{{ json_encode($selectedServices ?? []) }}">

                <div class="form-group">
                    <label>Select Services</label>
                    <select id="service_id" name="service_id[]" class="form-control @error('service_id') is-invalid @enderror js-example-basic-multiple w-100" style="width: 100% !important;" multiple="multiple" required>
                    </select>
                    @error('service_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                <div class="form-group">
                    <label>Booking Date:</label>
                    <?php
                        if (isset($date)) {
                            $now_date = substr_replace($date, ':', 13, 0);
                            $now_date = date('Y-m-d', strtotime($date));
                        } else {
                            $now_date = date('Y-m-d');
                        }
                    ?>
                    <div id="datepicker-popup-booking" class="input-group date datepicker">
                        <input type="text" name="due_date" id="due_date" value="@if(isset($due_date)){{$due_date}}@else{{$now_date}}@endif" class="form-control @error('due_date') is-invalid @enderror" required>
                        <span class="input-group-addon input-group-append border-left">
                            <span class="far fa-calendar input-group-text"></span>
                        </span>
                        @error('due_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col">
                        <?php
                            if (isset($date)) {
                                $now_time = date('H:i', strtotime($date));
                            } else {
                                $now_time = date('H:i');
                            }
                        ?>
                        <label>Booking Time:</label>
                        <div class="input-group date" id="timepicker-example" data-target-input="nearest">
                            <div class="input-group" data-target="#timepicker-example" data-toggle="datetimepicker">
                                <input type="text" name="due_time" value="@if(isset($due_time)){{$due_time}}@else '' @endif" id="due_time" class="form-control form-control-sm  datetimepicker-input @error('due_time') is-invalid @enderror" data-target="#timepicker-example" required/>
                                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>
                            </div>
                            @error('due_time')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Service Time (minutes):</label>
                        <div class="input-group date" data-target-input="nearest">
                            <div class="input-group">
                                <input type="text" name="service_time" value="@if(isset($service_time)){{$service_time}}@endif" 
                                    class="form-control @error('service_time') is-invalid @enderror" 
                                    id="service_time">
                                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>
                            </div>
                            @error('service_time')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- <div class="form-group">
                    <div class="input-group">
                        <input type="number" id="num_days" name="num_days" class="form-control @if(isset($pay_days) && $pay_days==0 || !isset($pay_days)) hide @endif" placeholder="Enter number of days 1 or greater" value="@if(isset($pay_days)){{$pay_days}}@else 1 @endif">
                    </div>
                </div> --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span 
                                    class="input-group-text bg-primary text-white"
                                    title="This price is modifiable if service price is not fixed, otherwise you cannot change it.">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i></span>
                                </div>
                                <input type="text" name="service_price" class="form-control" id="service_price">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white">{{ currency_symbol() }}</span>
                                </div>
                                <input type="text" name="total_price" class="form-control @error('total_price') is-invalid @enderror" value="@if(isset($total_price)){{$total_price}}@else{{old('total_price')}}@endif" id="total_price" readonly>
                                @error('total_price')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                
                <!-- Inventory Section -->
                <p>Inventories is optional</p>
                <div class="row">
                    <div class="col-12 col-md-8 mb-2 mb-md-0">
                        <label>Select Inventory</label>
                        <select id="inventory_id" class="form-control js-example-basic-single w-100" style="width: 100% !important;">
                            <option value="">Select Inventory</option>
                            @foreach($inventories as $inventory)
                                <option value="{{ $inventory->id }}" data-name="{{ $inventory->name }}">{{ $inventory->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="d-none d-md-block">&nbsp;</label>
                        <button type="button" class="btn btn-primary btn-block" id="add_or_submit_basket">Go</button>
                    </div>
                </div>

                <div class="mt-2 form-group" id="basket_table" style="{{ $booking_detail != '' ? '' : 'display:none;' }}">
                    <h4>Basket</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Inventory</th>
                                <th>
                                    Quantity
                                    <span class="text-primary ms-1" 
                                        style="cursor: help;" 
                                        title="You can enter decimal quantities! Examples: 1, 0.25, 0.5, 1.75, etc.">
                                        <i class="fas fa-info-circle"></i>
                                    </span>
                                </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="basket_body">
                            @if($booking_detail !== '')
                                @foreach ($booking_detail[0]->inventory_books as $book)
                                    <tr>
                                        <td>
                                            <input type="hidden" name="inventory_id[]" value="{{ $book->inventory_id }}"> {{ $book->inventory->name }}
                                        </td>
                                        <td>
                                            <input type="number" name="quantity[]" value="{{ $book->quantity }}" min="0.01" step="0.01" class="form-control" min="1" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <label style="color:red;">
                    @if($tax_info[0]->tax_status==1 && $tax_info[0]->amount!="") VAT {{$tax_info[0]->amount}} @if($tax_info[0]->vat==1)% @elseif($tax_info[0]->vat==0)£ @endif will apply. @endif
                </label>
            </div>
        </div>
    </div>
</div>

@if(empty($booking_id))
<div class="card mb-4 border border-info shadow-sm" style="border-radius: 8px;">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3" style="cursor: pointer;" id="toggle-advance-payment-header">
        <div class="d-flex align-items-center">
            <div class="custom-control custom-checkbox mr-2">
                <input type="checkbox" class="custom-control-input" id="collect_advance" name="collect_advance" value="1" {{ old('collect_advance') ? 'checked' : '' }}>
                <label class="custom-control-label font-weight-bold text-dark mb-0" for="collect_advance" style="cursor: pointer;">
                    <i class="fas fa-hand-holding-usd text-primary mr-1"></i> Collect Advance Deposit / Payment (Optional)
                </label>
            </div>
        </div>
        <span class="badge badge-outline-info font-weight-normal" id="advance-badge-preview" style="display: none;">0.00</span>
    </div>

    <div class="card-body p-3" id="advance-payment-container" style="{{ old('collect_advance') ? '' : 'display: none;' }}">
        <p class="text-muted small mb-3">
            If the customer is paying a partial deposit or full payment upfront today, enter the amount and payment details below. Otherwise, payment will be settled upon service completion.
        </p>

        <div class="row">
            <!-- Advance Amount -->
            <div class="col-md-6 mb-3">
                <label class="font-weight-bold text-dark mb-1">
                    Advance Amount <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-white">{{ currency_symbol() }}</span>
                    </div>
                    <input type="number" step="0.01" min="0" class="form-control font-weight-bold" 
                           id="advance_amount" name="advance_amount" 
                           placeholder="0.00" value="{{ old('advance_amount') }}">
                </div>
                <!-- Single Discount Button Checkbox -->
                <div class="mt-2 d-flex align-items-center flex-wrap">
                    <button type="button" 
                            class="btn btn-xs btn-outline-primary mr-2" 
                            id="btn-discount-toggle">
                        <i class="far fa-square mr-1" id="discount-check-icon"></i>
                        <span>Discount</span>
                    </button>
                    <input type="checkbox" name="is_discount" id="is_discount" value="1" style="display:none;" {{ old('is_discount') ? 'checked' : '' }}>
                    <input type="hidden" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}">
                    <input type="hidden" id="service_original_total" value="">

                    <span id="discount-calc-preview" class="badge badge-warning text-dark font-weight-bold" style="display: none; font-size: 11px; padding: 4px 8px;">
                        <i class="fas fa-tag mr-1"></i>Discount: {{ currency_symbol() }}<span id="discount-calc-amount">0.00</span> (New Total: {{ currency_symbol() }}<span id="discount-new-total">0.00</span>)
                    </span>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="col-md-6 mb-3">
                <label class="font-weight-bold text-dark mb-1">
                    Payment Method <span class="text-danger">*</span>
                </label>
                <select class="form-control" name="payment_method" id="advance_payment_method">
                    <option value="">-- Choose Payment Method --</option>
                    @if(isset($payment_methods) && $payment_methods->count() > 0)
                        @foreach($payment_methods as $pm)
                            <option value="{{ strtolower($pm->method) }}"
                                    data-fields="{{ json_encode($pm->fields ?? []) }}"
                                    data-description="{{ $pm->description ?? '' }}"
                                    {{ old('payment_method') == strtolower($pm->method) ? 'selected' : '' }}>
                                {{ $pm->method }}
                            </option>
                        @endforeach
                    @else
                        <option value="cash" selected>Cash</option>
                        <option value="card" data-fields='[{"name":"last_4_digits","label":"Card Last 4 Digits","type":"number","required":false}]'>Card</option>
                        <option value="bank transfer" data-fields='[{"name":"transaction_ref","label":"Transaction Reference #","type":"text","required":true}]'>Bank Transfer</option>
                        <option value="cheque" data-fields='[{"name":"cheque_number","label":"Cheque Number","type":"text","required":true}]'>Cheque</option>
                    @endif
                </select>
                <small id="advance-payment-method-desc" class="form-text text-muted mt-1" style="display:none;"></small>
            </div>
        </div>

        <!-- Dynamic Fields Container -->
        <div id="advance-dynamic-fields-wrapper" class="card bg-light border p-3 mb-3" style="display:none;">
            <h6 class="font-weight-bold text-dark mb-2" style="font-size: 13px;">
                <i class="fas fa-list-alt text-primary mr-1"></i> Transaction Details
            </h6>
            <div id="advance-dynamic-fields-container" class="row"></div>
        </div>

        <div class="row">
            <!-- Payment Date -->
            <div class="col-md-6 mb-3">
                <label class="font-weight-bold text-dark mb-1">Payment Date</label>
                <input type="date" class="form-control" name="advance_payment_date" value="{{ date('Y-m-d') }}">
            </div>

            <!-- Payment Proof Document (Max 2MB) -->
            <div class="col-md-6 mb-3">
                <label class="font-weight-bold text-dark mb-1">
                    Proof of Payment (Optional)
                </label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" id="advance_payment_file" name="payment_file" accept=".jpg,.jpeg,.png,.gif,.svg,.pdf,.doc,.docx">
                    <label class="custom-file-label text-truncate" for="advance_payment_file" id="advance_payment_file_label">Choose receipt or proof file...</label>
                </div>
                <small class="text-muted d-block mt-1">Accepted: PDF, JPG, PNG, DOCX (Max 2MB)</small>
                <div id="advance-file-size-error" class="text-danger small mt-1 font-weight-bold" style="display:none;">
                    <i class="fas fa-exclamation-circle mr-1"></i>File size exceeds 2MB. Please choose a smaller file.
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="form-group">
    <label for="exampleTextarea1">Comment</label>
    <textarea class="form-control" id="exampleTextarea1" name="comment" rows="4">@if(isset($comment)){{$comment}}@else{{old('comment')}}@endif</textarea>
</div>
@if(isset($service_time))
<button type="submit" class="btn btn-dark mr-2">Update Booking</button>
@else
<button type="submit" class="btn btn-primary mr-2">Create Booking</button>
@endif
<a class="btn btn-light" href="{{route('service-bookings.index')}}">Cancel</a>

<!-- Basket Modal -->
<div class="modal fade" id="basketModal" tabindex="-1" role="dialog" aria-labelledby="basketModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="basketModalLabel">Basket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table" id="basket_table" style="display: none;">
                    <thead>
                        <tr>
                            <th>Inventory Name</th>
                            <th>Quantity</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="basket_body">
                        <!-- Added items will appear here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitBasket">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Details Modal -->
<div class="modal fade" id="customerHistoryModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-dark" id="customerModalLabel">
                    <i class="fas fa-history text-primary mr-2"></i>Customer History
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <!-- Customer Details Card -->
                <div class="card mb-4 border shadow-none bg-light">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <h6 class="card-title mb-0 font-weight-bold text-primary">
                                <i class="fas fa-user mr-1"></i> Customer Information
                            </h6>
                            <div class="customer-birthday"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-3 mb-2">
                                <small class="text-muted d-block">Name</small>
                                <span class="font-weight-bold text-dark customer-name">-</span>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-2">
                                <small class="text-muted d-block">Phone</small>
                                <span class="font-weight-bold text-dark customer-primary-phone">-</span>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-2">
                                <small class="text-muted d-block">Email</small>
                                <span class="font-weight-bold text-dark customer-email">-</span>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-2">
                                <small class="text-muted d-block">Private Notes</small>
                                <span class="font-weight-bold text-dark customer-notes">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking History Section -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="card-title mb-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-check mr-1"></i> Past Bookings
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped" id="booking-table">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th>Date &amp; Time</th>
                                <th>Services</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX Data Will Populate Here -->
                        </tbody>
                    </table>
                </div>
                <!-- Load More Button -->
                <div class="text-center mt-3" id="loadMoreHistoryContainer" style="display:none;">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnLoadMoreHistory">
                        <i class="fas fa-spinner fa-spin mr-1 d-none" id="loadMoreSpinner"></i>
                        <i class="fas fa-chevron-down mr-1" id="loadMoreIcon"></i> Load More Bookings
                    </button>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <small class="text-muted" id="bookingCountInfo"></small>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Risky Service Confirmation Modal -->
<div class="modal fade" id="riskyServiceModal" tabindex="-1" role="dialog" aria-labelledby="riskyServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="riskyServiceModalLabel">Risky Service Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> <!-- Missing closing tag added here -->

            <div class="modal-body">
                <p>This service is marked as risky. Please confirm you understand and agree to proceed with this service.</p>
                <input type="hidden" id="risky_service_id">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="agreeRiskyService">I Agree</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Ensure all select2 elements have 100% responsive width without duplicate instantiation
    function applySelect2Responsive() {
        $('.js-example-basic-single').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    width: '100%',
                    dropdownAutoWidth: false
                });
            }
        });
        $('.js-example-basic-multiple').each(function() {
            if (!$(this).hasClass('select2-hidden-accessible')) {
                $(this).select2({
                    width: '100%',
                    dropdownAutoWidth: false
                });
            }
        });
        $('.select2-container:not(.select2-container--open)').css('width', '100%');
    }
    applySelect2Responsive();
    $(window).on('resize orientationchange', function() {
        $('.select2-container:not(.select2-container--open)').css('width', '100%');
    });
    var selectedServices = JSON.parse($('#selected_services').val() || '[]').map(String); // Ensure IDs are strings
    var lastSelectedServiceId = selectedServices.length > 0 ? selectedServices[selectedServices.length - 1] : null;
    var agreedRiskyServices = new Set(selectedServices); // Track services user has agreed to

    // Log initial state for debugging
    console.log('Initial selectedServices:', selectedServices);
    console.log('Initial user_id:', $('#user_id').val());
    console.log('Initial agreedRiskyServices:', Array.from(agreedRiskyServices));

    // Handle employee selection to populate services
    function populateServices(employeeId) {
        if (employeeId) {
            $.ajax({
                url: "{{ route('getEmployeeServices') }}",
                type: "GET",
                data: { employee_id: employeeId },
                success: function(response) {
                    console.log('getEmployeeServices response:', response);
                    $('#service_id').empty();
                    if (response.length > 0) {
                        // Store valid pre-selected services
                        var validSelectedServices = [];
                        $.each(response, function(key, service) {
                            var serviceId = service.id.toString();
                            var isSelected = selectedServices.includes(serviceId) ? 'selected' : '';
                            if (isSelected) {
                                validSelectedServices.push(serviceId);
                            }
                            $('#service_id').append(
                                `<option value="${service.id}" 
                                        data-price="${service.price}" 
                                        data-price-type="${service.price_type}" 
                                        data-risk-status="${service.risk_status}" 
                                        data-time="${service.service_time || 30}" 
                                        ${isSelected}>
                                    ${service.name}
                                </option>`
                            );
                        });

                        // Log valid selections
                        console.log('Valid selected services:', validSelectedServices);

                        // Update selectedServices
                        selectedServices = validSelectedServices;

                        // Reinitialize Select2
                        if ($('#service_id').hasClass('select2-hidden-accessible')) {
                            $('#service_id').select2('destroy');
                        }
                        $('#service_id').select2({
                            placeholder: "Select services",
                            allowClear: true,
                            width: '100%',
                            dropdownAutoWidth: false
                        });
                        $('#service_id').next('.select2-container').css('width', '100%');

                        // Trigger service price calculation
                        $('#service_id').trigger('change');
                    } else {
                        $('#service_id').append('<option value="">No services available</option>');
                        $('#service_price').val('').prop('readonly', true);
                        $('#total_price').val('').prop('readonly', true);
                        selectedServices = [];
                        lastSelectedServiceId = null;
                    }
                },
                error: function(xhr) {
                    console.error('Failed to fetch employee services:', xhr.responseText);
                    $('#service_id').empty().append('<option value="">Error loading services</option>');
                }
            });
        } else {
            $('#service_id').empty().append('<option value="">Select an employee first</option>');
            $('#service_price').val('').prop('readonly', true);
            $('#total_price').val('').prop('readonly', true);
            selectedServices = [];
            lastSelectedServiceId = null;
        }
    }

    // Bind employee change event
    $('#user_id').on('change', function() {
        var employeeId = $(this).val();
        console.log('user_id changed to:', employeeId);
        populateServices(employeeId);
    });

    // Handle service selection to update prices and show modal for risky services
    $('#service_id').on('change', function(e) {
        var selectedOptions = $(this).find('option:selected');
        var totalPrice = 0;
        var totalTime = 0;
        var lastServicePrice = 0;
        var lastServiceTime = 30; // default fallback
        var isServicePriceFixed = false;

        // Update selected services
        var currentSelectedIds = selectedOptions.map(function() {
            return $(this).val();
        }).get().map(String);

        // Find newly selected service
        var newlySelectedId = currentSelectedIds.find(id => !selectedServices.includes(id));
        if (newlySelectedId) {
            lastSelectedServiceId = newlySelectedId;
            var selectedOption = $('#service_id option[value="' + newlySelectedId + '"]');
            
            // Risky service check
            if (selectedOption.data('risk-status') === 'risky' && !agreedRiskyServices.has(newlySelectedId)) {
                $('#riskyServiceModalLabel').text('Risky Service Confirmation: ' + selectedOption.text());
                $('#risky_service_id').val(newlySelectedId);
                $('#riskyServiceModal').modal('show');
                return; // Stop here until user agrees
            }
        }

        // Find deselected service
        var deselectedId = selectedServices.find(id => !currentSelectedIds.includes(id));
        if (deselectedId) {
            agreedRiskyServices.delete(deselectedId);
        }

        // Calculate totals only for allowed services
        if (selectedOptions.length > 0) {
            if (!lastSelectedServiceId || !currentSelectedIds.includes(String(lastSelectedServiceId))) {
                lastSelectedServiceId = currentSelectedIds[currentSelectedIds.length - 1];
            }

            selectedOptions.each(function() {
                var serviceId = String($(this).val());
                var includeInCalc = $(this).data('risk-status') !== 'risky' || agreedRiskyServices.has(serviceId);

                if (includeInCalc) {
                    var price = parseFloat($(this).attr('data-price') || $(this).data('price')) || 0;
                    var time = parseFloat($(this).attr('data-time') || $(this).data('time')) || 30;

                    totalPrice += price;
                    totalTime += time;

                    if (serviceId === String(lastSelectedServiceId)) {
                        lastServicePrice = price;
                        lastServiceTime = time;
                        var pType = $(this).attr('data-price-type') || $(this).data('price-type') || $(this).data('priceType');
                        isServicePriceFixed = (pType === 'fixed');
                    }
                }
            });
        } else {
            lastSelectedServiceId = null;
            lastServiceTime = 30;
        }

        // Update inputs
        $('#service_price').val(lastServicePrice.toFixed(2))
            .prop('readonly', isServicePriceFixed || selectedOptions.length === 0);

        $('#total_price').val(totalPrice.toFixed(2));

        // === NEW: Update Service Time ===
        $('#service_time').val(totalTime);

        // Update selectedServices array
        selectedServices = currentSelectedIds;

        console.log('Total Service Time (minutes):', totalTime);
    });

    // Handle "I Agree" button for risky services
    $('#agreeRiskyService').on('click', function() {
        var serviceId = $('#risky_service_id').val();
        agreedRiskyServices.add(serviceId);
        $('#riskyServiceModal').modal('hide');
        // Trigger change to update prices
        $('#service_id').trigger('change');
    });

    // Handle modal close without agreeing
    $('#riskyServiceModal').on('hidden.bs.modal', function() {
        var serviceId = $('#risky_service_id').val();
        if (serviceId && !agreedRiskyServices.has(serviceId)) {
            // Remove the risky service from selection
            selectedServices = selectedServices.filter(id => id !== serviceId);
            $('#service_id').val(selectedServices).trigger('change.select2');
            $('#service_id').trigger('change');
        }
    });

    // Customer booking history pagination variables
    let currentHistoryCustomer = null;
    let currentHistoryPage = 1;
    let totalHistoryBookings = 0;
    let loadedHistoryBookingsCount = 0;

    function buildBookingRows(serviceInfo) {
        let currency = "{{ currency_symbol() }}";
        let html = '';
        $.each(serviceInfo, function(index, booking) {
            let total = booking.total_price || (booking.bookingdetail ? booking.bookingdetail.reduce((sum, detail) => sum + (detail.service ? parseFloat(detail.service.price) : 0), 0) : 0);
            let servicesList = (booking.bookingdetail || []).map(detail => {
                if (!detail.service) return '';
                let sColor = detail.service.service_color || '#4b49ac';
                let textColor = (sColor.startsWith('#fff') || sColor.startsWith('#FFF')) ? '#000' : '#fff';
                return `<span class="badge me-1 mb-1 badge-pill" style="background-color: ${sColor}; color: ${textColor};">${detail.service.name}</span>`;
            }).join(' ');

            let statusBadge = '';
            if (booking.service_status == 1) {
                statusBadge = '<span class="badge badge-info">Booked</span>';
            } else if (booking.service_status == 2) {
                statusBadge = '<span class="badge badge-warning">In-Progress</span>';
            } else if (booking.service_status == 3) {
                statusBadge = '<span class="badge badge-success">Completed</span>';
            } else if (booking.service_status == 4) {
                statusBadge = '<span class="badge badge-danger">Cancelled</span>';
            } else {
                statusBadge = '<span class="badge badge-secondary">-</span>';
            }

            html += `
                <tr>
                    <td>
                        <div class="font-weight-bold text-dark">${booking.due_date || '-'}</div>
                        <small class="text-muted">${booking.due_time || ''}</small>
                    </td>
                    <td style="max-width: 220px;">
                        ${servicesList || '<span class="text-muted fst-italic">No services</span>'}
                    </td>
                    <td>
                        <span class="font-weight-bold text-dark">${currency}${parseFloat(total).toFixed(2)}</span>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <small class="text-muted">${booking.comment ? booking.comment : '-'}</small>
                    </td>
                </tr>
            `;
        });
        return html;
    }

    function updateBookingCountInfo(loaded, total, hasMore) {
        if (total === 0) {
            $('#bookingCountInfo').text('No bookings found');
            $('#loadMoreHistoryContainer').hide();
        } else if (hasMore) {
            $('#bookingCountInfo').text(`Showing ${loaded} of ${total} bookings`);
            $('#loadMoreHistoryContainer').show();
        } else {
            $('#bookingCountInfo').text(`Showing all ${total} bookings`);
            $('#loadMoreHistoryContainer').hide();
        }
    }

    function fetchCustomerDetailsAndHistory(customer_id, page, append, autoOpen = true) {
        return $.ajax({
            type: "GET",
            url: "{{ url('customer-detail') }}",
            data: { customer_id: customer_id, page: page, limit: 5 },
            success: function(response) {
                if (response && response.customer) {
                    currentHistoryCustomer = customer_id;
                    currentHistoryPage = response.page;
                    totalHistoryBookings = response.total;

                    if (!append) {
                        $("#customer-detail").show();
                        let today = new Date().toISOString().slice(5, 10);
                        let customerBirthday = response.customer.date_of_birth ? response.customer.date_of_birth.slice(5, 10) : '';
                        $(".customer-name").html(response.customer.first_name + ' ' + response.customer.last_name);
                        $(".customer-primary-phone").html(response.customer.primary_phone);
                        if (customerBirthday === today) {
                            $(".customer-birthday").html(
                                `<span class="badge bg-warning text-dark">🎉 Birthday Today: ${response.customer.date_of_birth}</span>`
                            );
                        } else {
                            $(".customer-birthday").html("");
                        }
                        $(".customer-email").html(response.customer.email || '-');
                        $(".customer-notes").html(response.customer.private_notes || '-');

                        loadedHistoryBookingsCount = response.service_info.length;
                        if (response.service_info.length > 0) {
                            $("#booking-table tbody").html(buildBookingRows(response.service_info));
                        } else {
                            $("#booking-table tbody").html(`<tr><td colspan="5" class="text-center py-4 text-muted">No Past Bookings Found</td></tr>`);
                        }
                    } else {
                        loadedHistoryBookingsCount += response.service_info.length;
                        if (response.service_info.length > 0) {
                            $("#booking-table tbody").append(buildBookingRows(response.service_info));
                        }
                    }

                    updateBookingCountInfo(loadedHistoryBookingsCount, totalHistoryBookings, response.has_more);

                    // Auto-show modal only if autoOpen requested, first page and show_booking_history is enabled (1)
                    if (autoOpen && !append) {
                        if (parseInt(response.customer.show_booking_history) === 1 || response.customer.show_booking_history === undefined || response.customer.show_booking_history === null) {
                            $("#customerHistoryModal").modal('show');
                        }
                    }
                } else {
                    $("#customer-detail").hide();
                    $('#btnViewCustomerHistory').hide();
                }
            }
        });
    }

    // Handle customer selection for modal
    $('#customer_id').on('change', function() {
        var customer_id = $(this).val();
        if (customer_id) {
            $('#btnViewCustomerHistory').show();
            fetchCustomerDetailsAndHistory(customer_id, 1, false, true);
        } else {
            $('#btnViewCustomerHistory').hide();
            $('#loadMoreHistoryContainer').hide();
            $('#bookingCountInfo').text('');
        }
    });

    // Manual view history button
    $('#btnViewCustomerHistory').on('click', function() {
        var customer_id = $('#customer_id').val();
        if (!customer_id) return;
        if (currentHistoryCustomer !== customer_id) {
            fetchCustomerDetailsAndHistory(customer_id, 1, false, true);
        } else {
            $("#customerHistoryModal").modal('show');
        }
    });

    // Handle Load More button
    $('#btnLoadMoreHistory').on('click', function() {
        if (!currentHistoryCustomer) return;
        var $btn = $(this);
        $btn.prop('disabled', true);
        $('#loadMoreSpinner').removeClass('d-none');
        $('#loadMoreIcon').addClass('d-none');

        fetchCustomerDetailsAndHistory(currentHistoryCustomer, currentHistoryPage + 1, true, false).always(function() {
            $btn.prop('disabled', false);
            $('#loadMoreSpinner').addClass('d-none');
            $('#loadMoreIcon').removeClass('d-none');
        });
    });

    if ($('#customer_id').val()) {
        $('#btnViewCustomerHistory').show();
        fetchCustomerDetailsAndHistory($('#customer_id').val(), 1, false, false);
    }

    // Handle inventory basket
    $('#add_or_submit_basket').on('click', function() {
        var inventorySelect = $('#inventory_id');
        var inventoryId = inventorySelect.val();
        var inventoryName = inventorySelect.find('option:selected').text();
        var _token = "{{ csrf_token() }}";

        if (!inventoryId) {
            alert('Please select inventory.');
            return;
        }

        if ($(`#basket_body input[value="${inventoryId}"]`).length > 0) {
            alert('This inventory is already added.');
            return;
        }

        $.ajax({
            url: '/check-inventory-quantity',
            type: 'POST',
            data: {
                _token: _token,
                inventory_id: inventoryId
            },
            success: function(response) {
                // Out of stock
                if (response.quantity <= 0) {
                    swal({
                        title: "Out of Stock!",
                        text: `Inventory "${inventoryName}" is out of stock (${response.quantity})!`,
                        icon: "error",
                        button: "OK",
                        className: "swal-error-custom" // optional: for styling
                    });
                    return;
                }

                let rowClass = "";

                // Low stock warning
                if (response.quantity < response.low_alert) {
                    swal({
                        title: "Low Stock Warning",
                        text: `Warning: Inventory "${inventoryName}" quantity is low (${response.quantity})!`,
                        icon: "warning",
                        button: "OK",
                        className: "swal-warning-custom"
                    });
                    rowClass = "table-danger";
                }

                // Add to basket
                $('#basket_body').append(`
                    <tr class="${rowClass}">
                        <td>
                            <input type="hidden" name="inventory_id[]" value="${inventoryId}">
                            ${inventoryName}
                        </td>
                        <td>
                            <input type="number" class="form-control" name="quantity[]" min="0.01" step="0.01" value="1" max="${response.quantity}" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                        </td>
                    </tr>
                `);

                $('#basket_table').show();
                inventorySelect.val('').trigger('change'); // if using select2
            },
            error: function(xhr) {
                alert('Failed to check inventory. Please try again.');
                console.error(xhr.responseText);
            }
        });
    });

    $('#basket_body').on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        if ($('#basket_body').children().length === 0) {
            $('#basket_table').hide();
        }
    });

    // Handle payment upon completion checkbox
    $("#pay_upon").click(function() {
        if ($('#pay_upon').is(":checked")) {
            $('#num_days').addClass("hide");
            $('#num_days').prop('required', false);
        } else {
            $('#num_days').removeClass("hide");
            $('#num_days').prop('required', true);
        }
    });

    // Handle home service checkbox
    $('#is_home_service').on('change', function() {
        if ($(this).is(':checked')) {
            $('#address_field').slideDown();
            $('#booking_address').prop('required', true);
        } else {
            $('#address_field').slideUp();
            $('#booking_address').prop('required', false).val('');
        }
    });

    // Show address field if home service is already checked (edit mode)
    if ($('#is_home_service').is(':checked')) {
        $('#address_field').show();
    }

    // Initialize form for edit mode
    if ($('#user_id').val() && selectedServices.length > 0) {
        setTimeout(function() {
            console.log('Triggering user_id change for edit mode');
            populateServices($('#user_id').val());
        }, 100); // Delay to ensure DOM and Select2 are ready
    }

    // When user manually changes the service price, update total price accordingly
    $('#service_price').on('input keyup change', function() {
        var enteredVal = $(this).val();
        var enteredPrice = parseFloat(enteredVal);
        if (isNaN(enteredPrice) || enteredPrice < 0) {
            enteredPrice = 0;
        }

        var selectedOptions = $('#service_id').find('option:selected');
        if (selectedOptions.length === 0) {
            $('#total_price').val('');
            return;
        }

        // Determine which service option is being modified
        var targetOption = null;
        if (selectedOptions.length === 1) {
            targetOption = $(selectedOptions[0]);
        } else if (lastSelectedServiceId) {
            selectedOptions.each(function() {
                if (String($(this).val()) === String(lastSelectedServiceId)) {
                    targetOption = $(this);
                }
            });
        }

        // If targetOption still not found, find the first editable service
        if (!targetOption) {
            selectedOptions.each(function() {
                var pType = $(this).attr('data-price-type') || $(this).data('price-type') || $(this).data('priceType');
                if (pType !== 'fixed') {
                    targetOption = $(this);
                }
            });
        }

        // Update the target option's stored price with the custom price entered by user
        if (targetOption) {
            targetOption.data('price', enteredPrice).attr('data-price', enteredPrice);
        }

        // Recalculate total price across all selected services
        var totalPrice = 0;
        selectedOptions.each(function() {
            var serviceId = String($(this).val());
            var includeInCalc = $(this).data('risk-status') !== 'risky' || agreedRiskyServices.has(serviceId);
            if (includeInCalc) {
                var price = parseFloat($(this).attr('data-price') || $(this).data('price')) || 0;
                totalPrice += price;
            }
        });

        $('#service_original_total').val(totalPrice.toFixed(2));
        if ($('#is_discount').is(':checked')) {
            calculateDiscount();
        } else {
            $('#total_price').val(totalPrice.toFixed(2));
        }
    });

    // ==================== ADVANCE PAYMENT & DISCOUNT HANDLERS ====================
    $('#collect_advance').on('change', function() {
        if ($(this).is(':checked')) {
            $('#advance-payment-container').slideDown(200);
            $('#advance_amount').prop('required', true);
            $('#advance_payment_method').prop('required', true);
        } else {
            $('#advance-payment-container').slideUp(200);
            $('#advance_amount').prop('required', false);
            $('#advance_payment_method').prop('required', false);
            $('#advance_amount').val('');
            $('#advance-badge-preview').hide().text('');

            // Reset discount if advance turned off
            if ($('#is_discount').is(':checked')) {
                $('#btn-discount-toggle').trigger('click');
            }
        }
    });

    function calculateDiscount() {
        if (!$('#is_discount').is(':checked')) {
            return;
        }

        let original = parseFloat($('#service_original_total').val());
        if (isNaN(original) || original <= 0) {
            original = parseFloat($('#total_price').val()) || 0;
            if (original > 0) {
                $('#service_original_total').val(original.toFixed(2));
            }
        }

        const advance = parseFloat($('#advance_amount').val()) || 0;

        if (original > 0 && advance > 0 && advance < original) {
            const discount = original - advance;
            $('#discount_amount').val(discount.toFixed(2));
            $('#discount-calc-amount').text(discount.toFixed(2));
            $('#discount-new-total').text(advance.toFixed(2));
            $('#discount-calc-preview').show();
            $('#total_price').val(advance.toFixed(2));
        } else if (advance >= original && original > 0) {
            $('#discount_amount').val('0.00');
            $('#discount-calc-amount').text('0.00');
            $('#discount-new-total').text(advance.toFixed(2));
            $('#discount-calc-preview').hide();
        } else {
            $('#discount_amount').val('0.00');
            $('#discount-calc-preview').hide();
        }
    }

    // Single Discount Toggle Button (behaves like a checkbox button)
    $('#btn-discount-toggle').on('click', function() {
        const $chk = $('#is_discount');
        const isNowChecked = !$chk.is(':checked');
        $chk.prop('checked', isNowChecked);

        // Auto-open advance payment container if closed
        if (isNowChecked && !$('#collect_advance').is(':checked')) {
            $('#collect_advance').prop('checked', true).trigger('change');
        }

        if (isNowChecked) {
            const currentTotal = parseFloat($('#total_price').val()) || 0;
            if (currentTotal > 0 && !$('#service_original_total').val()) {
                $('#service_original_total').val(currentTotal.toFixed(2));
            }

            $(this).removeClass('btn-outline-primary').addClass('btn-primary text-white font-weight-bold');
            $('#discount-check-icon').removeClass('far fa-square').addClass('fas fa-check-square');
            $(this).find('span').text('Discount (Applied)');
            calculateDiscount();
        } else {
            $(this).removeClass('btn-primary text-white font-weight-bold').addClass('btn-outline-primary');
            $('#discount-check-icon').removeClass('fas fa-check-square').addClass('far fa-square');
            $(this).find('span').text('Discount');
            $('#discount-calc-preview').hide();
            $('#discount_amount').val('0.00');

            const orig = $('#service_original_total').val();
            if (orig) {
                $('#total_price').val(orig);
                $('#service_original_total').val('');
            }
        }
    });

    $('#advance_amount').on('input change', function() {
        const val = parseFloat($(this).val()) || 0;
        if (val > 0) {
            $('#advance-badge-preview').text('Deposit: ' + val.toFixed(2)).show();
        } else {
            $('#advance-badge-preview').hide().text('');
        }

        if ($('#is_discount').is(':checked')) {
            calculateDiscount();
        }
    });

    $('#advance_payment_method').on('change', function () {
        const $selected = $(this).find('option:selected');
        const description = $selected.data('description') || '';
        let fields = $selected.data('fields') || [];

        if (typeof fields === 'string') {
            try { fields = JSON.parse(fields); } catch(e) { fields = []; }
        }

        const $wrapper = $('#advance-dynamic-fields-wrapper');
        const $container = $('#advance-dynamic-fields-container');
        const $desc = $('#advance-payment-method-desc');

        if (description) {
            $desc.text(description).show();
        } else {
            $desc.hide();
        }

        $container.empty();

        if (Array.isArray(fields) && fields.length > 0) {
            fields.forEach(function (f) {
                const reqAttr = f.required ? 'required' : '';
                const reqStar = f.required ? '<span class="text-danger">*</span>' : '';
                const inputHtml = `
                    <div class="col-md-6 mb-2">
                        <label class="small font-weight-bold mb-1">${f.label} ${reqStar}</label>
                        <input type="${f.type || 'text'}" 
                               class="form-control form-control-sm" 
                               name="payment_details[${f.name}]" 
                               placeholder="Enter ${f.label}" 
                               ${reqAttr}>
                    </div>
                `;
                $container.append(inputHtml);
            });
            $wrapper.slideDown(200);
        } else {
            $wrapper.slideUp(200);
        }
    });

    $('#advance_payment_file').on('change', function() {
        const file = this.files[0];
        const $label = $('#advance_payment_file_label');
        const $err = $('#advance-file-size-error');

        if (!file) {
            $label.text('Choose receipt or proof file...');
            $err.hide();
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            $err.show();
            $(this).val('');
            $label.text('Choose receipt or proof file...');
        } else {
            $err.hide();
            $label.text(file.name);
        }
    });

});
</script>

<!-- GOOGLE PLACES ADDRESS AUTOCOMPLETE -->
@if(config('services.google.maps_api_key'))
<style>
    .pac-container {
        z-index: 99999 !important;
    }
    .places-new-suggestions-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 4px 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        list-style: none;
        margin: 0;
        padding: 0;
        max-height: 250px;
        overflow-y: auto;
        z-index: 99999;
        display: none;
    }
    .places-new-suggestion-item {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 13px;
        color: #333;
        border-bottom: 1px solid #f1f1f1;
        display: flex;
        align-items: center;
    }
    .places-new-suggestion-item:last-child {
        border-bottom: none;
    }
    .places-new-suggestion-item:hover {
        background-color: #f8f9fa;
        color: #007bff;
    }
    .places-new-suggestion-item i {
        color: #dc3545;
        margin-right: 8px;
        font-size: 12px;
    }
</style>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&v=weekly&callback=initGooglePlacesAutocomplete" async defer></script>
<script>
var modernPlacesInitialized = false;
var placesSessionToken = null;
var placesDebounceTimer = null;

async function initModernPlacesAutocomplete(input) {
    if (modernPlacesInitialized) return true;
    try {
        if (!google.maps.importLibrary) return false;
        var placesLib = await google.maps.importLibrary("places");
        if (!placesLib || !placesLib.AutocompleteSuggestion) return false;

        modernPlacesInitialized = true;
        placesSessionToken = new placesLib.AutocompleteSessionToken();

        var container = input.closest('.input-group') || input.parentElement;
        if (container) {
            container.style.position = 'relative';
        }

        var dropdown = document.getElementById('places_new_suggestions_list');
        if (!dropdown) {
            dropdown = document.createElement('ul');
            dropdown.id = 'places_new_suggestions_list';
            dropdown.className = 'places-new-suggestions-dropdown';
            (container || input.parentElement).appendChild(dropdown);
        }

        function closeDropdown() {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
        }

        input.addEventListener('input', function(e) {
            var query = e.target.value.trim();
            if (query.length < 2) {
                closeDropdown();
                return;
            }

            clearTimeout(placesDebounceTimer);
            placesDebounceTimer = setTimeout(async function() {
                try {
                    var request = {
                        input: query,
                        sessionToken: placesSessionToken
                    };
                    var response = await placesLib.AutocompleteSuggestion.fetchAutocompleteSuggestions(request);
                    var suggestions = (response && response.suggestions) ? response.suggestions : [];

                    if (!suggestions || suggestions.length === 0) {
                        closeDropdown();
                        return;
                    }

                    dropdown.innerHTML = '';
                    suggestions.forEach(function(sug) {
                        var text = (sug.placePrediction && sug.placePrediction.text && sug.placePrediction.text.text)
                            ? sug.placePrediction.text.text
                            : '';
                        if (!text) return;

                        var li = document.createElement('li');
                        li.className = 'places-new-suggestion-item';
                        li.innerHTML = '<i class="fas fa-map-marker-alt"></i><span>' + text + '</span>';
                        li.addEventListener('mousedown', function(evt) {
                            evt.preventDefault();
                            input.value = text;
                            closeDropdown();
                            placesSessionToken = new placesLib.AutocompleteSessionToken();
                        });
                        dropdown.appendChild(li);
                    });

                    dropdown.style.display = 'block';
                } catch (err) {
                    console.warn('Places API (New) fetch error:', err);
                    closeDropdown();
                }
            }, 250);
        });

        input.addEventListener('blur', function() {
            setTimeout(closeDropdown, 250);
        });

        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target) && e.target !== input) {
                closeDropdown();
            }
        });

        return true;
    } catch (e) {
        console.warn('Could not initialize Places API (New):', e);
        return false;
    }
}

async function initGooglePlacesAutocomplete() {
    var input = document.getElementById('booking_address');
    if (!input || typeof google === 'undefined' || !google.maps) {
        return;
    }

    // 1. Try modern Places API (New) AutocompleteSuggestion first
    var modernSuccess = await initModernPlacesAutocomplete(input);
    if (modernSuccess) {
        return;
    }

    // 2. Fallback to legacy google.maps.places.Autocomplete if available
    if (google.maps.places && google.maps.places.Autocomplete) {
        try {
            var autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode', 'establishment']
            });

            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                if (place && place.formatted_address) {
                    input.value = place.formatted_address;
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    var pacContainer = document.querySelector('.pac-container');
                    if (pacContainer && pacContainer.style.display !== 'none') {
                        e.preventDefault();
                    }
                }
            });
        } catch (e) {
            console.warn('Legacy Autocomplete error:', e);
        }
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof google !== 'undefined' && google.maps) {
            initGooglePlacesAutocomplete();
        }
    });
} else {
    if (typeof google !== 'undefined' && google.maps) {
        initGooglePlacesAutocomplete();
    }
}
</script>
@else
<!-- Fallback to Photon API if no Google Maps API Key is configured -->
<script>
(function() {
    let photonDebounce = null;
    const photonDatalistId = 'photon-places-list';

    // Create datalist element once
    if ($('#' + photonDatalistId).length === 0) {
        $('<datalist id="' + photonDatalistId + '"></datalist>').appendTo('body');
    }

    // Initialize autocomplete on the address field
    $(document).on('keyup', '#booking_address', function() {
        const query = $(this).val().trim();
        const $input = $(this);

        if (query.length < 3) return;

        clearTimeout(photonDebounce);

        photonDebounce = setTimeout(function() {
            const apiUrl = 'https://photon.komoot.io/api/?q=' + encodeURIComponent(query) + '&limit=8&lang=en';

            $.getJSON(apiUrl)
            .done(function(data) {
                if (!data.features || data.features.length === 0) return;

                const $datalist = $('#' + photonDatalistId);
                $datalist.empty();

                data.features.forEach(function(feature) {
                    const props = feature.properties;
                    const addressParts = [];

                    if (props.name) addressParts.push(props.name);
                    if (props.street) addressParts.push(props.street);
                    if (props.housenumber) addressParts.push(props.housenumber);
                    if (props.postcode) addressParts.push(props.postcode);
                    if (props.city) addressParts.push(props.city);
                    if (props.state) addressParts.push(props.state);
                    if (props.country) addressParts.push(props.country);

                    const displayText = addressParts.filter(Boolean).join(', ');

                    $datalist.append(
                        $('<option></option>')
                            .val(displayText)
                            .attr('data-lat', feature.geometry.coordinates[1])
                            .attr('data-lon', feature.geometry.coordinates[0])
                    );
                });

                $input.attr('list', photonDatalistId);
            })
            .fail(function(xhr, status, error) {
                console.log('Autocomplete error:', xhr.responseText || error);
            });
        }, 300);
    });
})();
</script>
@endif

