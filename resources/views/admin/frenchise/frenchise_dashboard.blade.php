@extends('layouts.admin')

@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard header items area -->
                    <div class="panel panel-default admin">
                        <div class="panel-heading admin-title">
                            <div class="product__header" style="border-bottom: none;">
                                <div class="row reorder-xs">
                                    <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                        <div class="product-header-title">
                                            <h2 style="font-size: 25px;">Frnchise Dashboard</h2>
                                        </div>
                                    </div>
                                    @include('includes.notification')
                                </div>   
                            </div>
                        </div>
                    
                        <div class="panel-body dashboard-body">
                            <div class="dashboard-header-area">
                                <div class="row">
                                    <div class="col-sm-12">
                                        @include('includes.form-success')
                                           
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a href="{{route('admin-frenchise-prod-index',['id'=>$fid])}}" class="title-stats title-red">
                                            <div class="icon"><i class="fa fa-shopping-cart fa-5x"></i></div>
                                            <div class="number">{{count($products)}}</div>
                                            <h4>Total Products!</h4>
                                            <span class="title-view-btn">View All</span>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a href="{{route('admin-frenchise-order-status',['id'=>$fid,'status' => 'pending'])}}" class="title-stats title-cyan">
                                            <div class="icon"><i class="fa fa-usd fa-5x"></i></div>
                                            <div class="number">{{count($pending)}}</div>
                                            <h4>Orders Pending!</h4>
                                            <span class="title-view-btn">View All</span>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a href="{{route('admin-frenchise-order-status',['id'=>$fid,'status' => 'processing'])}}" class="title-stats title-green">
                                            <div class="icon"><i class="fa fa-truck fa-5x"></i></div>
                                            <div class="number">{{count($processing)}}</div>
                                            <h4>Orders Procsessing!</h4>
                                            <span class="title-view-btn">View All</span>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a href="{{route('admin-frenchise-order-status',['id'=>$fid,'status' => 'completed'])}}" class="title-stats title-orange">
                                            <div class="icon"><i class="fa fa-check fa-5x"></i></div>
                                            <div class="number">{{count($completed)}}</div>
                                            <h4>Orders Completed!</h4>
                                            <span class="title-view-btn">View All</span>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a href="{{route('admin-frenchise-vendor-customer',['id'=>$fid])}}" class="title-stats title-purple">
                                            <div class="icon"><i class="fa fa-user fa-5x"></i></div>
                                            <div class="number">{{count($customer)}}</div>
                                            <h4>Total Customers!</h4>
                                            <span class="title-view-btn">View All</span>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a href="{{route('admin-frenchise-vendor-list',['id'=>$fid])}}" class="title-stats title-gray">
                                            <div class="icon"><i class="fa fa-fw fa-battery-half fa-5x"></i></div>
                                            <div class="number">
                                            {{$count_vendor}}
                                            </div>
                                            <h4>Total Vendor!</h4>
                                            <span class="title-view-btn">View All</span>
                                        </a>
                                    </div>
                                        <?php 
                                             
                                            $vendors = App\Models\User::where('frenchise_id',$frenchise->id)->get()->pluck('id');
                                            $gincome = 0;
                                            $nincome = 0; 
                                            foreach($vendors as $key)
                                            {
                                                $gincome=$gincome+(App\Models\Vendororder::where('user_id','=',$key)->sum('price'));
                                            }
                                           
                                            $detection = (($gincome * $currency_sign->value) * $gs->percentage_commission)/100;
                                        ?>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a  class="title-stats title-blue">
                                            <div class="icon"><i class="fa fa-at fa-5x"></i></div>
                                            <h4>Total Sale</h4>
                                            <div class="icon"><i class="fa fa-at fa-5x"></i></div>
                                            <div style="font-size: 38px; font-weight: 600;">{{$currency_sign->sign}}{{number_format($gincome  * $currency_sign->value,2)}}</div>
                                            <h4>Total Sale</h4>

                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a  class="title-stats title-blue">
                                            <div class="icon"><i class="fa fa-at fa-5x"></i></div>
                                            <h4>15% of Total Sale</h4>
                                                <div style="font-size: 38px; font-weight: 600;">{{$currency_sign->sign}}{{number_format($detection,2)}}</div>
                                            <h4>Gross Income</h4>
                                        </a>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                        <a  class="title-stats title-blue">
                                            <div class="icon"><i class="fa fa-at fa-5x"></i></div>
                                            <h4>30% of Gross Income</h4> 
                                            <div style="font-size: 38px; font-weight: 600;">{{$currency_sign->sign}}{{number_format((($detection*30)/100),2)}}</div>
                                            <h4>Net Income</h4>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <!-- Ending of Dashboard header items area -->

                    <!-- Starting of Dashboard Top reference + Most Used OS area -->
                    <div class="reference-OS-area">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="panel panel-default admin top-reference-area">
                                    <div class="panel-heading">Daily Chart</div>
                                    <div class="panel-body">
                                        <div id="chartContainer-topReference"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="panel panel-default admin top-reference-area">
                                    <div class="panel-heading">Monthly Chart</div>
                                    <div class="panel-body">
                                        <div id="chartContainer-os"></div>
                                        <a href="{{route('admin-chart-detail',$fid)}}">view details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="panel panel-default admin">
                        <div class="panel-heading admin-title">Total Sales of <?php echo date('Y') ?></div>
                            <div class="panel-body dashboard-body">
                                <div id="chtAnimatedBarChart" class="bcBar"></div>
                            </div>
                        </div>
                    <div>
                    
                    <!-- Ending of Dashboard Top reference + Most Used OS area -->

                    <!-- Starting of Dashboard Top reference + Most Used OS area -->
                    <div class="reference-OS-area">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6">
                                <div class="panel panel-default admin top-reference-area">
                                    <div class="panel-heading">Till End of Contract</div>
                                    <div class="panel-body">
                                        <div id="chartContainer-contract"></div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                                   
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script language="JavaScript">

    $(function() {
      //var chart_data = getData();
      var chart_data = {!! $yearlychart !!};

      $('#chtAnimatedBarChart').animatedBarChart({ data: chart_data });
   });

        displayLineChart();
        function displayLineChart() {
            var data = {
                labels: [
                    {!! $days !!}
                ],
                datasets: [
                    {
                        label: "Prime and Fibonacci",
                        fillColor: "#3dbcff",
                        strokeColor: "#0099ff",
                        pointColor: "rgba(220,220,220,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(220,220,220,1)",
                        data: [
                            {!! $sales !!}
                        ]
                    }
                ]
            };
            var ctx = document.getElementById("lineChart").getContext("2d");
            var options = {
                responsive: true
            };
            var lineChart = new Chart(ctx).Line(data, options);
        }
        </script>

<script type="text/javascript">

        var chart1 = new CanvasJS.Chart("chartContainer-topReference",
            {
                exportEnabled: true,
                animationEnabled: true,

                legend: {
                    cursor: "pointer",
                    horizontalAlign: "right",
                    verticalAlign: "center",
                    fontSize: 16,
                    padding: {
                        top: 20,
                        bottom: 2,
                        right: 20,
                    },
                },
                data: [
                    {
                        type: "pie",
                        showInLegend: true,
                        legendText: "",
                        toolTipContent: "{name}: <strong>{#percent%} (#percent%)</strong>",
                        indexLabel: "{y} (#percent%)",
                        percentFormatString: "#0.##",
                        indexLabelFontColor: "white",
                        indexLabelPlacement: "inside",
                        dataPoints: [
                            { y: {{$userSubscription_daily-(($userSubscription_daily/100*$frenchise->monthly_percentage)+($userSubscription_daily/100*$frenchise->sale_tax)+($userSubscription_daily/100*$frenchise->registration_tax)+($userSubscription_daily/100*$frenchise->other_expenses))}}, name: "Company" },
                            { y: {{$userSubscription_daily/100*$frenchise->monthly_percentage}}, name: "Monthly" },
                            { y: {{$userSubscription_daily/100*$frenchise->sale_tax}}, name: "Sale Tax" },
                            { y: {{$userSubscription_daily/100*$frenchise->registration_tax}},  name: "Registration Tax" },
                            { y: {{$userSubscription_daily/100*$frenchise->other_expenses}},  name: "Other Expenses" }
                        ]
                    }
                ]
            });
        calculatePercentage();
        chart1.render();

        function calculatePercentage() {
            var dataPoint = chart1.options.data[0].dataPoints;
            var total = dataPoint[0].y;
            for(var i = 0; i < dataPoint.length; i++) {
                if(i == 0) {
                    chart1.options.data[0].dataPoints[i].percentage = 100;
                } else {
                    chart1.options.data[0].dataPoints[i].percentage = ((dataPoint[i].y / total) * 100).toFixed(2);
                }
            }
        }


        var chart2 = new CanvasJS.Chart("chartContainer-os",
            {
                exportEnabled: true,
                animationEnabled: true,

                legend: {
                    cursor: "pointer",
                    horizontalAlign: "right",
                    verticalAlign: "center",
                    fontSize: 16,
                    padding: {
                        top: 20,
                        bottom: 2,
                        right: 20,
                    },
                },
                data: [
                    {
                        type: "pie",
                        showInLegend: true,
                        legendText: "",
                        toolTipContent: "{name}: <strong>{#percent%} (#percent%)</strong>",
                        indexLabel: "{y} (#percent%)",
                        percentFormatString: "#0.##",
                        indexLabelFontColor: "white",
                        indexLabelPlacement: "inside",
                        dataPoints: [
                            { y: {{$userSubscription_monthly-(($userSubscription_monthly/100*$frenchise->monthly_percentage)+($userSubscription_monthly/100*$frenchise->sale_tax)+($userSubscription_monthly/100*$frenchise->registration_tax)+($userSubscription_monthly/100*$frenchise->other_expenses))}}, name: "Company" },
                            { y: {{$userSubscription_monthly/100*$frenchise->monthly_percentage}}, name: "Monthly" },
                            { y: {{$userSubscription_monthly/100*$frenchise->sale_tax}}, name: "Sale Tax" },
                            { y: {{$userSubscription_monthly/100*$frenchise->registration_tax}},  name: "Registration Tax" },
                            { y: {{$userSubscription_monthly/100*$frenchise->other_expenses}},  name: "Other Expenses" }
                        ]
                    }
                ]
            });
        calculatePercentage2();
        chart2.render();

        function calculatePercentage2() {
            var dataPoint = chart2.options.data[0].dataPoints;
            var total = dataPoint[0].y;
            for(var i = 0; i < dataPoint.length; i++) {
                if(i == 0) {
                    chart2.options.data[0].dataPoints[i].percentage = 100;
                } else {
                    chart2.options.data[0].dataPoints[i].percentage = ((dataPoint[i].y / total) * 100).toFixed(2);
                }
            }
        }

        var chart3 = new CanvasJS.Chart("chartContainer-contract",
            {
                exportEnabled: true,
                animationEnabled: true,

                legend: {
                    cursor: "pointer",
                    horizontalAlign: "right",
                    verticalAlign: "center",
                    fontSize: 16,
                    padding: {
                        top: 20,
                        bottom: 2,
                        right: 20,
                    },
                },
                data: [
                    {
                        type: "pie",
                        showInLegend: true,
                        legendText: "",
                        toolTipContent: "{name}: <strong>{#percent%} (#percent%)</strong>",
                        indexLabel: "{y} (#percent%)",
                        percentFormatString: "#0.##",
                        indexLabelFontColor: "white",
                        indexLabelPlacement: "inside",
                        dataPoints: [
                            { y: {{$userSubscription_contract-(($userSubscription_contract/100*$frenchise->monthly_percentage)+($userSubscription_contract/100*$frenchise->sale_tax)+($userSubscription_contract/100*$frenchise->registration_tax)+($userSubscription_contract/100*$frenchise->other_expenses))}}, name: "Company" },
                            { y: {{$userSubscription_contract/100*$frenchise->monthly_percentage}}, name: "Monthly" },
                            { y: {{$userSubscription_contract/100*$frenchise->sale_tax}}, name: "Sale Tax" },
                            { y: {{$userSubscription_contract/100*$frenchise->registration_tax}},  name: "Registration Tax" },
                            { y: {{$userSubscription_contract/100*$frenchise->other_expenses}},  name: "Other Expenses" }
                        ]
                    }
                ]
            });
        calculatePercentage3();
        chart3.render();

        function calculatePercentage3() {
            var dataPoint = chart3.options.data[0].dataPoints;
            var total = dataPoint[0].y;
            for(var i = 0; i < dataPoint.length; i++) {
                if(i == 0) {
                    chart3.options.data[0].dataPoints[i].percentage = 100;
                } else {
                    chart3.options.data[0].dataPoints[i].percentage = ((dataPoint[i].y / total) * 100).toFixed(2);
                }
            }
        }



        // var chart = new CanvasJS.Chart("chartContainer-os",
        //     {
        //         exportEnabled: true,
        //         animationEnabled: true,
        //         legend: {
        //             cursor: "pointer",
        //             horizontalAlign: "right",
        //             verticalAlign: "center",
        //             fontSize: 16,
        //             padding: {
        //                 top: 20,
        //                 bottom: 2,
        //                 right: 20,
        //             },
        //         },
        //         data: [
        //             {
        //                 type: "pie",
        //                 showInLegend: true,
        //                 legendText: "",
        //                 toolTipContent: "{name}: <strong>{#percent%} (#percent%)</strong>",
        //                 indexLabel: "#percent%",
        //                 indexLabelFontColor: "white",
        //                 indexLabelPlacement: "inside",
        //                 dataPoints: [
        //                     @foreach($browsers as $browser)
        //                         {y:{{$browser->total_count}}, name: "{{$browser->referral}}"},
        //                     @endforeach
        //                 ]
        //             }
        //         ]
        //     });
        // chart.render();

        
        
        
    
</script>
@endsection