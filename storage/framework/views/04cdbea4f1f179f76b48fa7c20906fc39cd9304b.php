<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
        <ul>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<?php if($booking_detail != ''): ?>
    <?php $__currentLoopData = $booking_detail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $booking_id = $book->id;
            $customer_id = $book->customer_id;
            $user_id = $book->user_id;
            $due_date = $book->due_date;
            $due_time = $book->due_time;
            $finish_time = $book->finish_time;
            $total_price = $book->total_price;
            $comment = $book->comment;
            $pay_days = $book->payment_days;
        ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <input type="hidden" name="booking_id" value="<?php echo e($booking_id); ?>">
<?php endif; ?>

<style>
    .hide {
        display: none;
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <div class="form-group row">
                        <!-- Survey section commented out -->
                    </div>
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
                        <input type="text" name="due_date" id="due_date" value="<?php if(isset($due_date)): ?><?php echo e($due_date); ?><?php else: ?><?php echo e($now_date); ?><?php endif; ?>" class="form-control" required>
                        <span class="input-group-addon input-group-append border-left">
                            <span class="far fa-calendar input-group-text"></span>
                        </span>
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
                                <input type="text" name="due_time" value="<?php if(isset($due_time)): ?><?php echo e($due_time); ?><?php else: ?> '' <?php endif; ?>" id="due_time" class="form-control form-control-sm datetimepicker-input" data-target="#timepicker-example" required/>
                                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label>Finish Time (minutes):</label>
                        <div class="input-group date" data-target-input="nearest">
                            <div class="input-group">
                                <input type="text" name="finish_time" value="<?php if(isset($finish_time)): ?><?php echo e($finish_time); ?><?php else: ?> 30 <?php endif; ?>" class="form-control" maxlength="4"/>
                                <div class="input-group-addon input-group-append"><i class="far fa-clock input-group-text"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Select Customer</label>
                    <select id="customer_id" name="customer_id" class="form-control js-example-basic-single w-100" required>
                        <?php if($booking_detail == ''): ?>
                            <option value="">Select Customer</option>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($customer->id); ?>"><?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?> (<?php echo e($customer->primary_phone); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <option value="">Select Customer</option>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($customer->id); ?>" <?php if($customer_id==$customer->id): ?> selected <?php endif; ?> ><?php echo e($customer->first_name); ?> <?php echo e($customer->last_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Select Employee</label>
                    <select id="user_id" name="user_id" class="form-control js-example-basic-single w-100" required>
                        <?php if($booking_detail == ''): ?>
                            <option value="">Select employee</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>"><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <option value="">Select employee</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($user->id); ?>" <?php if($user_id==$user->id): ?> selected <?php endif; ?> ><?php echo e($user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </select>
                </div>
                <br>

                <input type="hidden" id="selected_services" value="<?php echo e(json_encode($selectedServices ?? [])); ?>">

                <div class="form-group">
                    <label>Select Services</label>
                    <select id="service_id" name="service_id[]" class="form-control js-example-basic-multiple w-100" multiple="multiple" required>
                    </select>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <input type="number" id="num_days" name="num_days" class="form-control <?php if(isset($pay_days) && $pay_days==0 || !isset($pay_days)): ?> hide <?php endif; ?>" placeholder="Enter number of days 1 or greater" value="<?php if(isset($pay_days)): ?><?php echo e($pay_days); ?><?php else: ?> 1 <?php endif; ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i class="fa fa-info-circle" aria-hidden="true"></i></span>
                                </div>
                                <input type="text" name="service_price" class="form-control" id="service_price">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-dollar-sign"></i></span>
                                </div>
                                <input type="text" name="total_price" class="form-control" value="<?php if(isset($total_price)): ?><?php echo e($total_price); ?><?php else: ?><?php echo e(old('total_price')); ?><?php endif; ?>" id="total_price" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Section -->
                <div class="row">
                    <div class="col-md-8">
                        <label>Select Inventory</label>
                        <select id="inventory_id" class="form-control js-example-basic-single w-100">
                            <option value="">Select Inventory</option>
                            <?php $__currentLoopData = $inventories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inventory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($inventory->id); ?>" data-name="<?php echo e($inventory->name); ?>"><?php echo e($inventory->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label> </label>
                        <button type="button" class="btn btn-primary btn-block" id="add_or_submit_basket">Go</button>
                    </div>
                </div>

                <div class="form-group" id="basket_table" style="<?php echo e($booking_detail != '' ? '' : 'display:none;'); ?>">
                    <h4>Basket</h4>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Inventory</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="basket_body">
                            <?php if($booking_detail !== ''): ?>
                                <?php $__currentLoopData = $booking_detail[0]->inventory_books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="inventory_id[]" value="<?php echo e($book->inventory_id); ?>"> <?php echo e($book->inventory->name); ?>

                                        </td>
                                        <td>
                                            <input type="number" name="quantity[]" value="<?php echo e($book->quantity); ?>" class="form-control" min="1" required>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <label style="color:red;">
                    <?php if($tax_info[0]->tax_status==1 && $tax_info[0]->amount!=""): ?> VAT <?php echo e($tax_info[0]->amount); ?> <?php if($tax_info[0]->vat==1): ?>% <?php elseif($tax_info[0]->vat==0): ?>£ <?php endif; ?> will apply. <?php endif; ?>
                </label>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="exampleTextarea1">Comment</label>
    <textarea class="form-control" id="exampleTextarea1" name="comment" rows="4"><?php if(isset($comment)): ?><?php echo e($comment); ?><?php else: ?><?php echo e(old('comment')); ?><?php endif; ?></textarea>
</div>

<button type="submit" class="btn btn-primary mr-2">Submit</button>
<a class="btn btn-light" href="<?php echo e(route('service-bookings.index')); ?>">Cancel</a>

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
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Customer Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <h4>Customer Information</h4>
                <div class="row">
                    <div class="col-md-6"><strong>Name:</strong> <span class="customer-name"></span></div>
                    <div class="col-md-6"><strong>Email:</strong> <span class="customer-email"></span></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><strong>Phone:</strong> <span class="customer-primary-phone"></span></div>
                    <div class="col-md-6"><strong>Private Notes:</strong> <span class="customer-notes"></span></div>
                </div>
                <div class="row">
                    <div class="col-md-6"><strong></strong> <span class="customer-birthday"></span></div>
                </div>
                <h4 class="mt-3">Booking History</h4>
                <div class="table-responsive">
                    <table class="table table-bordered" id="booking-table">
                        <thead>
                            <tr>
                                <th>Total Price</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Services</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AJAX Data Will Populate Here -->
                        </tbody>
                    </table>
                </div>
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
                url: "<?php echo e(route('getEmployeeServices')); ?>",
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
                                `<option value="${service.id}" data-price="${service.price}" data-price-type="${service.price_type}" data-risk-status="${service.risk_status}" ${isSelected}>
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
                            allowClear: true
                        });

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
        var lastServicePrice = 0;
        var isServicePriceFixed = false;

        // Update selected services
        var currentSelectedIds = selectedOptions.map(function() {
            return $(this).val();
        }).get().map(String);

        // Find newly selected service
        var newlySelectedId = currentSelectedIds.find(id => !selectedServices.includes(id));
        if (newlySelectedId) {
            lastSelectedServiceId = newlySelectedId;
            // Check if the newly selected service is risky
            var selectedOption = $('#service_id option[value="' + newlySelectedId + '"]');
            if (selectedOption.data('risk-status') === 'risky' && !agreedRiskyServices.has(newlySelectedId)) {
                $('#riskyServiceModalLabel').text('Risky Service Confirmation: ' + selectedOption.text());
                $('#risky_service_id').val(newlySelectedId);
                $('#riskyServiceModal').modal('show');
            }
        }

        // Find deselected service
        var deselectedId = selectedServices.find(id => !currentSelectedIds.includes(id));
        if (deselectedId) {
            agreedRiskyServices.delete(deselectedId); // Remove from agreed services
        }

        // Update prices only for agreed or non-risky services
        if (selectedOptions.length > 0) {
            selectedOptions.each(function() {
                var serviceId = $(this).val();
                // Only include price if service is not risky or user has agreed
                if ($(this).data('risk-status') !== 'risky' || agreedRiskyServices.has(serviceId)) {
                    var price = parseFloat($(this).data('price')) || 0;
                    totalPrice += price;
                    if (serviceId === lastSelectedServiceId) {
                        lastServicePrice = price;
                        isServicePriceFixed = $(this).data('price-type') === 'fixed';
                    }
                }
            });
        } else {
            lastSelectedServiceId = null;
        }

        // Update price inputs
        $('#service_price').val(lastServicePrice.toFixed(2)).prop('readonly', isServicePriceFixed || selectedOptions.length === 0);
        $('#total_price').val(totalPrice.toFixed(2)).prop('readonly', true);

        // Update selectedServices
        selectedServices = currentSelectedIds;
        console.log('Updated selectedServices:', selectedServices);
        console.log('Updated agreedRiskyServices:', Array.from(agreedRiskyServices));
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

    // Handle customer selection for modal
    $('#customer_id').on('change', function() {
        var customer_id = $(this).val();
        if (customer_id) {
            $.ajax({
                type: "GET",
                url: "<?php echo e(url('customer-detail')); ?>",
                data: { customer_id },
                success: function(response) {
                    if (response) {
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
                        $(".customer-email").html(response.customer.email);
                        $(".customer-notes").html(response.customer.private_notes);

                        var bookingsHtml = '';
                        if (response.service_info.length > 0) {
                            $.each(response.service_info, function(index, booking) {
                                bookingsHtml += `
                                    <tr>
                                        <td>(Total Price: $${booking.bookingdetail.reduce((sum, detail) => sum + detail.service.price, 0)})</td>
                                        <td>${booking.due_date}</td>
                                        <td>${booking.due_time}</td>
                                        <td>
                                            <ul>
                                                ${booking.bookingdetail.map(detail => `<li>${detail.service.name} - $${detail.service.price}</li>`).join('')}
                                            </ul>
                                        </td>
                                        <td>${booking.comment}</td>
                                    </tr>
                                `;
                            });
                        } else {
                            bookingsHtml = `<tr><td colspan="6">No Bookings Found</td></tr>`;
                        }
                        $("#booking-table tbody").html(bookingsHtml);
                        $("#customerModal").modal('show');
                    } else {
                        $("#customer-detail").hide();
                    }
                }
            });
        }
    });

    // Handle inventory basket
    $('#add_or_submit_basket').on('click', function() {
        var inventorySelect = $('#inventory_id');
        var inventoryId = inventorySelect.val();
        var inventoryName = inventorySelect.find('option:selected').text();
        var _token = "<?php echo e(csrf_token()); ?>";

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
                if (response.quantity <= 0) {
                    alert(`Inventory "${inventoryName}" is out of stock (${response.quantity})!`);
                    return;
                }
                let rowClass = "";
                if (response.quantity < response.low_alert) {
                    alert(`Warning: Inventory "${inventoryName}" quantity is low (${response.quantity})!`);
                    rowClass = "table-danger";
                }
                $('#basket_body').append(`
                    <tr class="${rowClass}">
                        <td>
                            <input type="hidden" name="inventory_id[]" value="${inventoryId}">
                            ${inventoryName}
                        </td>
                        <td>
                            <input type="number" class="form-control" name="quantity[]" value="" min="1" required>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                        </td>
                    </tr>
                `);
                $('#basket_table').show();
                inventorySelect.val('');
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

    // Initialize form for edit mode
    if ($('#user_id').val() && selectedServices.length > 0) {
        setTimeout(function() {
            console.log('Triggering user_id change for edit mode');
            populateServices($('#user_id').val());
        }, 100); // Delay to ensure DOM and Select2 are ready
    }
});
</script><?php /**PATH D:\Working\Code Disk\works fahd\beauty-saloon\resources\views/service_bookings/booking_form.blade.php ENDPATH**/ ?>