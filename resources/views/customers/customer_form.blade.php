

<p class="card-description">



    Personal info



</p>



<div class="row">



    <div class="col-md-6">



        <div class="form-group row">



            <label class="col-sm-3 col-form-label">First Name*</label>



            <div class="col-sm-9">



                <input type="text" id="firstname" name="first_name" minlength="2" value="@if(isset($customer->first_name)){{$customer->first_name}}@else{{old('first_name')}}@endif" class="form-control @error('first_name') is-invalid @enderror" placeholder="" />
                @error('first_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror



            </div>



        </div>



    </div>



    <div class="col-md-6">



        <div class="form-group row">



            <label class="col-sm-3 col-form-label">Last Name</label>



            <div class="col-sm-9">



                <input type="text" id="lastname" name="last_name" value="@if(isset($customer->last_name)){{$customer->last_name}}@else{{old('last_name')}}@endif" class="form-control @error('last_name') is-invalid @enderror" placeholder="" />
                @error('last_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror



            </div>



        </div>



    </div>



</div>



<div class="row">


    <div class="col-md-6">



        <!-- <div class="form-group row">



            <label class="col-sm-3 col-form-label">Mobile*</label>



            <div class="col-sm-9">



                <input type="text" id="primary_phone" name="primary_phone" minlength="11"  maxlength="14" value="@if(isset($customer->primary_phone)){{$customer->primary_phone}}@else{{old('primary_phone')}}@endif" class="form-control" maxlength="11"  placeholder=""/>



            </div>


            

        </div> -->
		<div class="form-group row">
			<label class="col-sm-3 col-form-label">Mobile*</label>
			<div class="col-sm-9">

                    <div class="row">

                            <div class="col-md-3 input-group-prepend">
                                <select id="tags" name="country_code" class="js-example-basic-single w-100 @error('country_code') is-invalid @enderror" required>

                                @foreach ($countries as $country)
                                    
                                <option value="{{ $country->phone_code }}" 
                                    {{ old('country_code', $customer->country_code ?? '') == $country->phone_code ? 'selected' : '' }}>
                                    {{ $country->phone_code }}
                                </option>

                                @endforeach

                                </select>
                                @error('country_code')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
				
                            <div class="col-md-9">
                                <input type="text" id="primary_phone" name="primary_phone" minlength="10"  maxlength="14" value="@if(isset($customer->primary_phone)){{$customer->primary_phone}}@else{{old('primary_phone')}}@endif" class="form-control @error('primary_phone') is-invalid @enderror" maxlength="11"  placeholder=""/>
                                @error('primary_phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                    </div>
			</div>
		</div>



    </div>






    <div class="col-md-6">
        <div class="form-group row">
            <label class="col-sm-3 col-form-label">Date Of Birth</label>
            <div id="datepicker-popup-new" class="col-sm-9 input-group date datepicker">
                <input type="text"name="date_of_birth" value="@if(isset($customer->date_of_birth)){{$customer->date_of_birth}}@else{{ old('date_of_birth')}}@endif" class="form-control">
                <span class="input-group-addon input-group-append border-left">
                    <span class="far fa-calendar input-group-text"></span>
                </span>
            </div>
        </div>
    </div>


</div>



<div class="row">



    <div class="col-md-6">



        <div class="form-group row">



            <label class="col-sm-3 col-form-label">Email</label>



            <div class="col">



                <input class="form-control" type="email" name="email" value="@if(isset($customer->email)){{$customer->email}}@else{{old('email')}}@endif"  placeholder=""/>



            </div>



        </div>



    </div>



    <div class="col-md-6">



        <!-- <div class="form-group row">



            <label class="col-sm-3 col-form-label">Postcode*</label>



            <div class="col-sm-9" id="lookup_field" >



                <input type="hidden" id="zip_code" value="@if(isset($customer->zip)){{$customer->zip}}@endif" class="form-control" />

           

            </div>



        </div> -->

        <div class="form-group row">



            <label class="col-sm-3 col-form-label">Address</label>



            <div class="col">



                <input class="form-control" type="text" name="address" value="@if(isset($customer->address)){{$customer->address}}@else{{old('address')}}@endif"  placeholder=""/>



            </div>



        </div>



    </div>



</div>



<div class="form-group">



    <label for="exampleTextarea1">Private Notes*</label>



    <textarea class="form-control" id="exampleTextarea1" name="private_notes" rows="4">@if(isset($customer->private_notes)){{$customer->private_notes}}@else{{old('private_notes')}}@endif</textarea>



</div>

<input type="hidden" id="latitude" value="" name="latitude">

<input type="hidden" id="longitude" value="" name="longitude">

<!-- <div class="form-group">



    <label for="referred_by">Referred By</label>



    <input id="tags" name="referred_by" class="form-control" value="@if(isset($customer->referred_by)){{$customer->referred_by}}@else other @endif" />



</div> -->

<div class="form-group">



                      <label>Referred By</label>



                      <select id="tags" name="referred_by" class="js-example-basic-single w-100">

                        @foreach($referreds as $ref)
                            <option value="{{ $ref->referred_by }}"
                                @if(isset($customer->referred_by) && $customer->referred_by == $ref->referred_by) selected @endif>
                                {{ Ucfirst($ref->referred_by) }}
                            </option>
                        @endforeach

                      </select>



                    </div>

<button type="submit" class="btn btn-primary mr-2">Submit</button>



<a class="btn btn-light" href="{{route('customers.index')}}">Cancel</a>