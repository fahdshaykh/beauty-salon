
<?php $__env->startSection('pagescript'); ?>
<script src="<?php echo e(asset('js/select2.js')); ?>"></script>
<script src="<?php echo e(asset('js/formpickers.js')); ?>"></script>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('title', 'Services Heat Map | '.Session::get('site_name')); ?>

<?php $__env->startSection('content'); ?>
<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                Services Heat Map
            </h3>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Services Heat Map</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group">
                            <input type="text" class="form-control daterange" name="graph_dates" id="graph_dates" />
                        </div>
                        <div class="form-group">
                            <select class="form-control form-control-lg" name="service_id" id="service_id">
                                <option value="">All Service</option>
                                <?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <span id="graph-done" class="btn btn-primary mt-3 ml-5">Search</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="col-md-12 grid-margin stretch-card mt-4">
            <div class="card">
            <div class="card-body" id="chartBar">
                <h4 class="card-title">
                <i class="fa fa-flag"></i>
                    Services Heat Map
                </h4>
                <div id="chart_div" style="width:100%">
                    <h1>Loading.......</h1>
                </div>                 
            </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	$('.daterange').daterangepicker();
</script>
<script type="text/javascript">
  $('#loader-modal').removeClass( "hide");
      google.charts.load("current", {
        "packages":["map"],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        "mapsApiKey": "AIzaSyBCcV570TyjIEhvmOrSaMgxBLiUHa5RxXU"
      });
      $( document ).ready(function() {
        var graph_dates = $("#graph_dates").val();
        google.charts.setOnLoadCallback(drawChart(graph_dates,''));
    });
    function drawChart(graph_dates,service) {
    
        var base_path = "show-heat-chart";
        $.ajax({
                type: "post",
                url: base_path,
                data: { graphs_dates:graph_dates,service:service, _token: "<?php echo e(csrf_token()); ?>" },
                success: function(response) {
                    console.log(response);
                    var responsedata = [];
                    var Header= ['City','Population'];
                    responsedata.push(Header);
                    if (response.length>0) {
                        $.each(response, function(index, value) {
                            var temp=[];
                            var string = 'Customer: '+value.first_name+' '+value.last_name+' Service: '+value.name;
                            temp.push(value.zip);
                            temp.push(string);
                            responsedata.push(temp);
                        });

                        var data = google.visualization.arrayToDataTable(responsedata);
                        var options = {
                            mapType: 'normal',
                            zoomLevel: 8,
                            showTooltip: true,
                            showInfoWindow: true,
                            useMapTypeControl: true,
                            enableScrollWheel:true,
                        };
                        document.getElementById('chart_div').innerHTML = "";
                        var map = new google.visualization.Map(document.getElementById('chart_div'));
                        map.draw(data,options);
                        
                    }
                    else{
                        alert("No data found");
                        document.getElementById('chart_div').innerHTML = "";
                    }
                
                
            }
        });
    }
    
    $('#graph-done').on('click', function() {
        document.getElementById('chart_div').innerHTML = "<h1>Loading......</h1>";
        var graph_dates = $("#graph_dates").val();
        var service = $("#service_id").val();
        drawChart(graph_dates,service);
        
    });
   
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Working\Code Disk\works fahd\beauty-salon\resources\views/reports/heatmap.blade.php ENDPATH**/ ?>