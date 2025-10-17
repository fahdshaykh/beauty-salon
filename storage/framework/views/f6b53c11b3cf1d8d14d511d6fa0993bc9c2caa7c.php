

<?php if($errors->any()): ?>



    <div class="alert alert-danger alert-dismissible">



        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



        <ul>



            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>



                <li><?php echo e($error); ?></li>



            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



        </ul>



    </div>



<?php endif; ?>



<?php if(Session::has('success')): ?>



    <div class="alert alert-success alert-dismissible">



        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>



        <?php echo e(Session::get('success')); ?>




    </div>



<?php endif; ?>



<p class="card-description">



    Personal info



</p>



<div class="row">



    <div class="col-md-6">



        <div class="form-group row">



            <label class="col-sm-3 col-form-label">First Name*</label>



            <div class="col-sm-9">



                <input type="text" id="firstname" name="first_name" minlength="2" value="<?php if(isset($customer->first_name)): ?><?php echo e($customer->first_name); ?><?php else: ?><?php echo e(old('first_name')); ?><?php endif; ?>" class="form-control" placeholder="" />



            </div>



        </div>



    </div>



    <div class="col-md-6">



        <div class="form-group row">



            <label class="col-sm-3 col-form-label">Last Name</label>



            <div class="col-sm-9">



                <input type="text" id="lastname" name="last_name" value="<?php if(isset($customer->last_name)): ?><?php echo e($customer->last_name); ?><?php else: ?><?php echo e(old('last_name')); ?><?php endif; ?>" class="form-control" placeholder="" />



            </div>



        </div>



    </div>



</div>



<div class="row">


    <div class="col-md-6">



        <!-- <div class="form-group row">



            <label class="col-sm-3 col-form-label">Mobile*</label>



            <div class="col-sm-9">



                <input type="text" id="primary_phone" name="primary_phone" minlength="11"  maxlength="14" value="<?php if(isset($customer->primary_phone)): ?><?php echo e($customer->primary_phone); ?><?php else: ?><?php echo e(old('primary_phone')); ?><?php endif; ?>" class="form-control" maxlength="11"  placeholder=""/>



            </div>


            

        </div> -->
		<div class="form-group row">
			<label class="col-sm-3 col-form-label">Mobile*</label>
			<div class="input-group col-sm-9">
				<div class="input-group-prepend">
					<select id="tags" name="country_code" class="js-example-basic-single w-100">

					<?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						
                    <option value="<?php echo e($country->phone_code); ?>" 
                        <?php echo e(old('country_code', $customer->country_code ?? '') == $country->phone_code ? 'selected' : ''); ?>>
                        +<?php echo e($country->phone_code); ?>

                    </option>

					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					</select>
				</div>
				
				<input type="text" id="primary_phone" name="primary_phone" minlength="11"  maxlength="14" value="<?php if(isset($customer->primary_phone)): ?><?php echo e($customer->primary_phone); ?><?php else: ?><?php echo e(old('primary_phone')); ?><?php endif; ?>" class="form-control" maxlength="11"  placeholder=""/>

			</div>
		</div>



    </div>



    <div class="col-md-6">



        <div class="form-group row">



            <label class="col-sm-3 col-form-label">Date Of Birth</label>



            <div class="col-sm-9">


                <input type="date" name="date_of_birth" value="<?php if(isset($customer->date_of_birth)): ?><?php echo e($customer->date_of_birth); ?><?php else: ?><?php echo e(old('date_of_birth')); ?><?php endif; ?>" class="form-control"   placeholder="" />



            </div>



        </div>



    </div>



</div>



<div class="row">



    <div class="col-md-6">



        <div class="form-group row">



            <label class="col-sm-3 col-form-label">Email</label>



            <div class="col">



                <input class="form-control" type="email" name="email" value="<?php if(isset($customer->email)): ?><?php echo e($customer->email); ?><?php else: ?><?php echo e(old('email')); ?><?php endif; ?>"  placeholder=""/>



            </div>



        </div>



    </div>



    <div class="col-md-6">



        <!-- <div class="form-group row">



            <label class="col-sm-3 col-form-label">Postcode*</label>



            <div class="col-sm-9" id="lookup_field" >



                <input type="hidden" id="zip_code" value="<?php if(isset($customer->zip)): ?><?php echo e($customer->zip); ?><?php endif; ?>" class="form-control" />

           

            </div>



        </div> -->

        <div class="form-group row">



            <label class="col-sm-3 col-form-label">Address</label>



            <div class="col">



                <input class="form-control" type="text" name="address" value="<?php if(isset($customer->address)): ?><?php echo e($customer->address); ?><?php else: ?><?php echo e(old('address')); ?><?php endif; ?>"  placeholder=""/>



            </div>



        </div>



    </div>



</div>



<div class="form-group">



    <label for="exampleTextarea1">Private Notes*</label>



    <textarea class="form-control" id="exampleTextarea1" name="address" rows="4"><?php if(isset($customer->address)): ?><?php echo e($customer->address); ?><?php else: ?><?php echo e(old('address')); ?><?php endif; ?></textarea>



</div>

<input type="hidden" id="latitude" value="" name="latitude">

<input type="hidden" id="longitude" value="" name="longitude">

<!-- <div class="form-group">



    <label for="referred_by">Referred By</label>



    <input id="tags" name="referred_by" class="form-control" value="<?php if(isset($customer->referred_by)): ?><?php echo e($customer->referred_by); ?><?php else: ?> other <?php endif; ?>" />



</div> -->

<div class="form-group">



                      <label>Referred By</label>



                      <select id="tags" name="referred_by" class="js-example-basic-single w-100">

                      <option value="Google" <?php if(isset($customer->referred_by) && $customer->referred_by =='Google'): ?> selected <?php endif; ?> >Google</option>

                      <option value="Leaflet"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Leaflet'): ?> selected <?php endif; ?> >Leaflet</option>

                      <option value="Recommendation" <?php if(isset($customer->referred_by) && $customer->referred_by =='Recommendation'): ?> selected <?php endif; ?>>Recommendation</option>

                      <option value="Repeat client"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Repeat client'): ?> selected <?php endif; ?>>Repeat client</option>

                      <option value="Seen van"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Seen van'): ?> selected <?php endif; ?>>Seen van</option>

                      <option value="Paper advert"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Paper advert'): ?> selected <?php endif; ?>>Paper advert</option>

                      <option value="Facebook"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Facebook'): ?> selected <?php endif; ?>>Facebook</option>
                      <option value="Instagram"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Instagram'): ?> selected <?php endif; ?>>Instagram</option>
                      <option value="Email"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Email'): ?> selected <?php endif; ?>>Email</option>
                      <option value="Postcard"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Postcard'): ?> selected <?php endif; ?>>Postcard</option>
                      <option value="SMS"  <?php if(isset($customer->referred_by) && $customer->referred_by =='SMS'): ?> selected <?php endif; ?>>SMS</option>
                      <option value="AdWords"  <?php if(isset($customer->referred_by) && $customer->referred_by =='AdWords'): ?> selected <?php endif; ?>>AdWords</option>
                      <option value="Magazine"  <?php if(isset($customer->referred_by) && $customer->referred_by =='Magazine'): ?> selected <?php endif; ?>>Magazine</option>

                      </select>



                    </div>

<button type="submit" class="btn btn-primary mr-2">Submit</button>



<a class="btn btn-light" href="<?php echo e(route('customers.index')); ?>">Cancel</a><?php /**PATH D:\Work\Code Disk\works side\bsaloon\resources\views/customers/customer_form.blade.php ENDPATH**/ ?>