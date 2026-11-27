<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $setting->page_title ?: $company->company_name }} | Online Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root {
            --primary: {{ $setting->primary_color ?: '#4b49ac' }};
            --primary-hover: {{ $setting->secondary_color ?: '#3f3e91' }};
            --primary-light: {{ $setting->primary_color ?: '#4b49ac' }}15;
            --dark: #1e293b;
            --gray-bg: #f8fafc;
            --border: #e2e8f0;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--gray-bg);
            color: var(--dark);
            margin: 0;
            padding-bottom: 90px;
        }

        .booking-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-hover) 100%);
            color: #ffffff;
            padding: 44px 20px 68px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        @if($setting->banner_image)
        .booking-hero {
            background: linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)), url('{{ asset($setting->banner_image) }}');
            background-size: cover;
            background-position: center;
        }
        @endif

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            padding: 6px 16px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .booking-container {
            max-width: 900px;
            margin: -40px auto 0;
            padding: 0 16px;
            position: relative;
            z-index: 10;
        }

        .booking-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.2s ease;
            position: relative;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-num {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: var(--dark);
        }

        /* Mode Selector */
        .mode-card {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }
        .mode-card.active, .mode-card:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .mode-card i {
            font-size: 24px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        /* Category Filter Pills */
        .cat-pills {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 10px;
            margin-bottom: 16px;
            scrollbar-width: thin;
        }
        .cat-pill {
            padding: 8px 16px;
            border-radius: 20px;
            background: #f1f5f9;
            color: var(--dark);
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }
        .cat-pill.active {
            background: var(--primary);
            color: #ffffff;
        }

        /* Service Cards */
        .service-item {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }
        .service-item:hover {
            border-color: var(--primary);
            background: #fafafa;
        }
        .service-item.selected {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .service-info h6 {
            font-weight: 700;
            margin-bottom: 4px;
        }
        .service-meta {
            font-size: 13px;
            color: var(--text-muted);
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .service-price {
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
        }

        /* Technician Picker */
        .tech-card {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tech-card.active {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .tech-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 6px;
        }

        /* Time Slots */
        .slot-pill {
            padding: 10px 14px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid var(--border);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }
        .slot-pill:hover, .slot-pill.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        /* Floating AI Widget */
        .ai-float-btn {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #ffffff;
            border: none;
            border-radius: 30px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
            cursor: pointer;
            z-index: 1050;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ai-float-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 25px rgba(99, 102, 241, 0.5);
        }

        /* AI Chat Drawer */
        .ai-drawer {
            position: fixed;
            bottom: 85px;
            right: 24px;
            width: 360px;
            max-width: calc(100vw - 48px);
            height: 480px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.18);
            border: 1px solid var(--border);
            display: none;
            flex-direction: column;
            z-index: 1050;
            overflow: hidden;
            animation: slideUp 0.25s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .ai-header {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #ffffff;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ai-body {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 13px;
        }
        .ai-msg {
            max-width: 82%;
            padding: 10px 14px;
            border-radius: 12px;
            line-height: 1.4;
        }
        .ai-msg.bot {
            background: #f1f5f9;
            align-self: flex-start;
            border-bottom-left-radius: 2px;
        }
        .ai-msg.user {
            background: var(--primary);
            color: #ffffff;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
        }
        .ai-footer {
            padding: 12px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 8px;
        }

        /* Bottom Sticky Summary Bar on Mobile */
        .sticky-book-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.06);
        }

        {!! $setting->custom_css !!}
    </style>
</head>
<body>

    <!-- Hero Header Block -->
    <div class="booking-hero">
        <div class="hero-badge">
            <i class="fas fa-shield-alt"></i> Verified Service Provider
        </div>
        <h2 class="font-weight-bold mb-2">{{ $setting->page_title ?: $company->company_name }}</h2>
        <p class="mb-2 text-white-50">{{ $setting->tagline ?: 'Book your service online easily & reliably' }}</p>
        @if(!empty($company->company_address) || !empty($company->company_phone))
        <div class="small text-white-50 d-flex justify-content-center gap-3">
            @if(!empty($company->company_address))
                <span class="mr-3"><i class="fas fa-map-marker-alt mr-1"></i> {{ $company->company_address }}</span>
            @endif
            @if(!empty($company->company_phone))
                <span><i class="fas fa-phone mr-1"></i> {{ $company->company_phone }}</span>
            @endif
        </div>
        @endif
    </div>

    <div class="booking-container">

        <!-- Form Tag Wrapping All Steps -->
        <form id="publicBookingForm">
            @csrf

            <!-- 1. Service Location Mode Block -->
            @if($setting->allowed_service_modes == 'both')
            <div class="booking-card">
                <div class="section-header">
                    <span class="section-num">1</span>
                    <h5 class="section-title">Where should the service take place?</h5>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="mode-card active" data-mode="in_shop">
                            <i class="fas fa-store"></i>
                            <h6 class="font-weight-bold mb-1">At Facility / Shop</h6>
                            <small class="text-muted">Visit our location for your appointment</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mode-card" data-mode="home_service">
                            <i class="fas fa-truck-pickup"></i>
                            <h6 class="font-weight-bold mb-1">Home / On-Site</h6>
                            <small class="text-muted">We send a technician to your address</small>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="is_home_service" id="is_home_service_input" value="0">
            </div>
            @else
                <input type="hidden" name="is_home_service" id="is_home_service_input" value="{{ $setting->allowed_service_modes == 'home_service' ? 1 : 0 }}">
            @endif

            <!-- 2. Services Selection Block -->
            <div class="booking-card" id="services_section">
                <div class="section-header">
                    <span class="section-num">2</span>
                    <div>
                        <h5 class="section-title">Select Services</h5>
                        <small class="text-muted">Choose one or more services to book</small>
                    </div>
                </div>

                <!-- Category Filter Pills -->
                <div class="cat-pills">
                    <button type="button" class="cat-pill active" data-cat="all">All Services</button>
                    @foreach($categories as $category)
                        <button type="button" class="cat-pill" data-cat="cat_{{ $category->id }}">{{ $category->name }}</button>
                    @endforeach
                </div>

                <!-- Service Items -->
                <div class="services-list">
                    @forelse($categories as $category)
                        @foreach($category->services_booking as $service)
                            <div class="service-item cat-item cat_{{ $category->id }}" data-id="{{ $service->id }}" data-price="{{ $service->price }}" data-duration="{{ $service->duration ?: 30 }}" data-name="{{ $service->name }}">
                                <div class="service-info pr-3">
                                    <h6>{{ $service->name }}</h6>
                                    <div class="service-meta">
                                        <span><i class="far fa-clock mr-1"></i> {{ $service->duration ?: 30 }} mins</span>
                                        <span class="badge badge-light border">{{ $category->name }}</span>
                                    </div>
                                    @if($service->description)
                                        <p class="text-muted small mt-1 mb-0">{{ Str::limit($service->description, 90) }}</p>
                                    @endif
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="service-price mb-2">${{ number_format($service->price, 2) }}</div>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-select-service rounded-pill px-3">
                                        <i class="fas fa-plus mr-1"></i> Add
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <p class="text-center text-muted py-4">No services currently published.</p>
                    @endforelse
                </div>
            </div>

            <!-- 3. Specialist / Technician Selection Block -->
            @if($setting->enable_technician_picker && $technicians->isNotEmpty())
            <div class="booking-card">
                <div class="section-header">
                    <span class="section-num">3</span>
                    <div>
                        <h5 class="section-title">Select Specialist / Technician</h5>
                        <small class="text-muted">Choose a preference or let us assign the best available</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6 col-md-3 mb-2">
                        <div class="tech-card active" data-tech-id="">
                            <div class="tech-avatar bg-light border d-flex align-items-center justify-content-center mx-auto text-primary font-weight-bold">
                                <i class="fas fa-users"></i>
                            </div>
                            <h6 class="font-weight-bold mb-0 small">Any Available</h6>
                            <small class="text-muted font-italic">Fastest slot</small>
                        </div>
                    </div>
                    @foreach($technicians as $tech)
                        <div class="col-6 col-md-3 mb-2">
                            <div class="tech-card" data-tech-id="{{ $tech->id }}">
                                @if($tech->image && file_exists(public_path('profiles/' . $tech->image)))
                                    <img src="{{ asset('profiles/' . $tech->image) }}" class="tech-avatar mx-auto" alt="{{ $tech->name }}">
                                @else
                                    <div class="tech-avatar bg-light border d-flex align-items-center justify-content-center mx-auto text-dark font-weight-bold">
                                        {{ strtoupper(substr($tech->name, 0, 2)) }}
                                    </div>
                                @endif
                                <h6 class="font-weight-bold mb-0 small">{{ $tech->name }}</h6>
                                <small class="text-muted">{{ $tech->position ?: 'Specialist' }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
                <input type="hidden" name="user_id" id="user_id_input" value="">
            </div>
            @endif

            <!-- 4. Date & Time Slot Picker Block -->
            <div class="booking-card">
                <div class="section-header">
                    <span class="section-num">4</span>
                    <div>
                        <h5 class="section-title">Appointment Date & Time</h5>
                        <small class="text-muted">Select an available slot</small>
                    </div>
                </div>

                <!-- Date Picker Input -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="font-weight-bold small">Pick a Date</label>
                        <input type="date" name="due_date" id="due_date_input" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <!-- Available Slots Container -->
                <label class="font-weight-bold small mb-2">Available Slots for Selected Date</label>
                <div id="slots_container" class="row">
                    <div class="col-12 py-3 text-center text-muted">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Loading real-time open slots...
                    </div>
                </div>
                <input type="hidden" name="due_time" id="due_time_input" required>
            </div>

            <!-- 5. Customer Details & Service Address Block -->
            <div class="booking-card">
                <div class="section-header">
                    <span class="section-num">5</span>
                    <div>
                        <h5 class="section-title">Your Contact & Location Details</h5>
                        <small class="text-muted">Where and to whom should we confirm the appointment?</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">First Name <span class="text-danger">*</span></label>
                        <input type="text" name="first_name" class="form-control" placeholder="John" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Last Name</label>
                        <input type="text" name="last_name" class="form-control" placeholder="Doe">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Phone Number <span class="text-danger">*</span></label>
                        <input type="tel" name="customer_phone" class="form-control" placeholder="e.g. +1 555-0199" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label class="small font-weight-bold">Email Address</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="john@example.com">
                    </div>
                </div>

                <!-- Home Service Address Box -->
                <div id="home_address_container" style="display: none;">
                    <div class="form-group">
                        <label class="small font-weight-bold">Service Location / Street Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="booking_address" id="booking_address" class="form-control" placeholder="House/Flat #, Street, Area">
                            <div class="input-group-append">
                                <span class="input-group-text bg-white"><i class="fas fa-map-marker-alt text-danger"></i></span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($setting->enable_customer_notes)
                <div class="form-group mb-0">
                    <label class="small font-weight-bold">Notes / Problem Description</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any specific requirements or instructions for our technician..."></textarea>
                </div>
                @endif
            </div>

            <!-- 6. Summary & Submission Block -->
            <div class="booking-card">
                <div class="section-header">
                    <span class="section-num">6</span>
                    <h5 class="section-title">Review & Confirm</h5>
                </div>

                <div id="selected_services_summary" class="mb-3">
                    <p class="text-muted small">No services selected yet. Please select at least one service above.</p>
                </div>

                <div class="d-flex justify-content-between align-items-center py-2 border-top">
                    <span class="font-weight-bold">Total Estimated Cost:</span>
                    <span class="h4 font-weight-bold text-primary mb-0" id="total_price_display">$0.00</span>
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold mt-4 shadow" id="btnSubmitBooking" style="background: var(--primary); border: none;">
                    Confirm & Complete Booking <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>

        </form>
    </div>

    <!-- Floating AI Booking Assistant Button -->
    @if($setting->enable_ai_assistant)
    <button type="button" class="ai-float-btn" id="btnToggleAi">
        <i class="fas fa-magic"></i> <span>Ask AI Assistant</span>
    </button>

    <!-- AI Chat Drawer -->
    <div class="ai-drawer" id="aiDrawer">
        <div class="ai-header">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-robot mr-1"></i>
                <span class="font-weight-bold">{{ $companySetting->ai_assistant_name ?? 'Smart Booking Assistant' }}</span>
            </div>
            <button type="button" class="close text-white" id="btnCloseAi">&times;</button>
        </div>
        <div class="ai-body" id="aiChatBody">
            <div class="ai-msg bot">
                👋 Hello! Tell me what service you need or describe your issue, and I will recommend the right service and find open time slots for you!
            </div>
        </div>
        <div class="ai-footer">
            <input type="text" id="aiUserInput" class="form-control form-control-sm" placeholder="e.g. need AC repair tomorrow morning">
            <button type="button" class="btn btn-sm btn-primary" id="btnSendAi"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    @endif

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function () {
            let selectedServices = {};
            const slug = "{{ $setting->slug }}";

            // Service Mode Toggle (Shop vs Home)
            $('.mode-card').click(function () {
                $('.mode-card').removeClass('active');
                $(this).addClass('active');
                const mode = $(this).data('mode');
                $('#is_home_service_input').val(mode === 'home_service' ? 1 : 0);
                if (mode === 'home_service') {
                    $('#home_address_container').slideDown();
                    $('#booking_address').prop('required', true);
                } else {
                    $('#home_address_container').slideUp();
                    $('#booking_address').prop('required', false);
                }
            });

            // Category Filter
            $('.cat-pill').click(function () {
                $('.cat-pill').removeClass('active');
                $(this).addClass('active');
                const cat = $(this).data('cat');
                if (cat === 'all') {
                    $('.cat-item').show();
                } else {
                    $('.cat-item').hide();
                    $('.' + cat).show();
                }
            });

            // Service Selection Toggle
            $('.service-item').click(function (e) {
                if ($(e.target).closest('button').length === 0 && !$(e.target).hasClass('service-item')) return;
                const id = $(this).data('id');
                const name = $(this).data('name');
                const price = parseFloat($(this).data('price')) || 0;
                const duration = parseInt($(this).data('duration')) || 30;

                if (selectedServices[id]) {
                    delete selectedServices[id];
                    $(this).removeClass('selected');
                    $(this).find('.btn-select-service').html('<i class="fas fa-plus mr-1"></i> Add').removeClass('btn-primary').addClass('btn-outline-primary');
                } else {
                    selectedServices[id] = { id, name, price, duration };
                    $(this).addClass('selected');
                    $(this).find('.btn-select-service').html('<i class="fas fa-check mr-1"></i> Added').removeClass('btn-outline-primary').addClass('btn-primary');
                }

                updateSummary();
                loadSlots();
            });

            // Technician Selection
            $('.tech-card').click(function () {
                $('.tech-card').removeClass('active');
                $(this).addClass('active');
                const techId = $(this).data('tech-id');
                $('#user_id_input').val(techId);
                loadSlots();
            });

            // Date Change
            $('#due_date_input').change(function () {
                loadSlots();
            });

            // Load Slots via AJAX
            function loadSlots() {
                const date = $('#due_date_input').val();
                const userId = $('#user_id_input').val();
                let totalDuration = 0;
                Object.values(selectedServices).forEach(s => totalDuration += s.duration);
                if (totalDuration === 0) totalDuration = 30;

                $('#slots_container').html('<div class="col-12 py-3 text-center text-muted"><i class="fas fa-spinner fa-spin mr-1"></i> Checking available slots...</div>');

                $.get(`/book/${slug}/slots`, { date, user_id: userId, duration: totalDuration }, function (res) {
                    if (res.success && res.slots.length > 0) {
                        let html = '';
                        res.slots.forEach(slot => {
                            html += `
                                <div class="col-4 col-md-3 mb-2">
                                    <div class="slot-pill" data-time="${slot.time_24}">
                                        ${slot.time_12}
                                    </div>
                                </div>`;
                        });
                        $('#slots_container').html(html);

                        // Select slot listener
                        $('.slot-pill').click(function () {
                            $('.slot-pill').removeClass('active');
                            $(this).addClass('active');
                            $('#due_time_input').val($(this).data('time'));
                        });
                    } else {
                        $('#slots_container').html('<div class="col-12 py-3 text-center text-danger small"><i class="fas fa-calendar-times mr-1"></i> No slots available for this date. Please pick another date.</div>');
                        $('#due_time_input').val('');
                    }
                });
            }

            // Update Price & Services Summary
            function updateSummary() {
                const keys = Object.keys(selectedServices);
                if (keys.length === 0) {
                    $('#selected_services_summary').html('<p class="text-muted small">No services selected yet. Please select at least one service above.</p>');
                    $('#total_price_display').text('$0.00');
                    return;
                }

                let html = '<ul class="list-group list-group-flush mb-2">';
                let total = 0;
                let totalMin = 0;

                keys.forEach(k => {
                    const item = selectedServices[k];
                    total += item.price;
                    totalMin += item.duration;
                    html += `
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1 bg-transparent">
                            <span>${item.name} <small class="text-muted">(${item.duration}m)</small></span>
                            <span class="font-weight-bold">$${item.price.toFixed(2)}</span>
                        </li>`;
                });
                html += '</ul>';

                $('#selected_services_summary').html(html);
                $('#total_price_display').text('$' + total.toFixed(2));
            }

            // Initial slot loading
            loadSlots();

            // Submit Booking Form
            $('#publicBookingForm').submit(function (e) {
                e.preventDefault();

                const serviceIds = Object.keys(selectedServices);
                if (serviceIds.length === 0) {
                    alert('Please select at least one service before proceeding.');
                    $('html, body').animate({ scrollTop: $('#services_section').offset().top - 20 }, 300);
                    return;
                }

                if (!$('#due_time_input').val()) {
                    alert('Please choose an available appointment time slot.');
                    return;
                }

                const formData = $(this).serializeArray();
                serviceIds.forEach(id => {
                    formData.push({ name: 'service_ids[]', value: id });
                });

                $('#btnSubmitBooking').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Confirming Appointment...');

                $.ajax({
                    url: `/book/${slug}/submit`,
                    method: 'POST',
                    data: formData,
                    success: function (res) {
                        if (res.success && res.redirect_url) {
                            window.location.href = res.redirect_url;
                        } else {
                            alert(res.message || 'Booking submission failed. Please try again.');
                            $('#btnSubmitBooking').prop('disabled', false).html('Confirm & Complete Booking <i class="fas fa-arrow-right ml-2"></i>');
                        }
                    },
                    error: function (xhr) {
                        const err = xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred. Please check your inputs.';
                        alert(err);
                        $('#btnSubmitBooking').prop('disabled', false).html('Confirm & Complete Booking <i class="fas fa-arrow-right ml-2"></i>');
                    }
                });
            });

            // AI Chat Assistant Logic
            $('#btnToggleAi, #btnCloseAi').click(function () {
                $('#aiDrawer').fadeToggle(200);
            });

            let chatHistory = [];

            $('#btnSendAi').click(sendAiMessage);
            $('#aiUserInput').keypress(function (e) {
                if (e.which === 13) sendAiMessage();
            });

            function sendAiMessage() {
                const text = $.trim($('#aiUserInput').val());
                if (!text) return;

                $('#aiChatBody').append(`<div class="ai-msg user">${text}</div>`);
                $('#aiUserInput').val('');
                $('#aiChatBody').scrollTop($('#aiChatBody')[0].scrollHeight);

                $('#aiChatBody').append(`<div class="ai-msg bot" id="aiLoadingMsg"><i class="fas fa-spinner fa-spin mr-1"></i> Thinking...</div>`);
                $('#aiChatBody').scrollTop($('#aiChatBody')[0].scrollHeight);

                $.ajax({
                    url: `/book/${slug}/ai-assistant`,
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    data: { message: text, history: chatHistory },
                    success: function (res) {
                        $('#aiLoadingMsg').remove();
                        if (res.success) {
                            $('#aiChatBody').append(`<div class="ai-msg bot">${res.message}</div>`);

                            // If AI suggested actions, add 1-click apply button
                            if (res.suggested_actions && res.suggested_actions.service_ids && res.suggested_actions.service_ids.length > 0) {
                                const actionBtn = $('<button class="btn btn-xs btn-primary btn-sm mt-2 rounded-pill"><i class="fas fa-magic mr-1"></i> Apply Recommendation to Booking</button>');
                                actionBtn.click(function () {
                                    applyAiSuggestions(res.suggested_actions);
                                });
                                $('#aiChatBody').append($('<div class="ai-msg bot bg-transparent p-0"></div>').append(actionBtn));
                            }

                            chatHistory.push({ sender: 'user', text: text });
                            chatHistory.push({ sender: 'bot', text: res.message });
                        } else {
                            $('#aiChatBody').append(`<div class="ai-msg bot text-danger">Sorry, I could not process that. Please browse the service list below!</div>`);
                        }
                        $('#aiChatBody').scrollTop($('#aiChatBody')[0].scrollHeight);
                    },
                    error: function () {
                        $('#aiLoadingMsg').remove();
                        $('#aiChatBody').append(`<div class="ai-msg bot text-danger">Service temporarily busy. Please choose from the list below.</div>`);
                    }
                });
            }

            function applyAiSuggestions(actions) {
                // Select Mode
                if (actions.mode) {
                    $(`.mode-card[data-mode="${actions.mode}"]`).click();
                }

                // Select Services
                if (actions.service_ids && actions.service_ids.length > 0) {
                    actions.service_ids.forEach(id => {
                        const item = $(`.service-item[data-id="${id}"]`);
                        if (item.length && !selectedServices[id]) {
                            item.click();
                        }
                    });
                }

                // Select Preferred Date
                if (actions.preferred_date) {
                    $('#due_date_input').val(actions.preferred_date).change();
                }

                $('#aiDrawer').fadeOut(200);
                $('html, body').animate({ scrollTop: $('#services_section').offset().top - 20 }, 400);
            }
        });
    </script>
</body>
</html>
