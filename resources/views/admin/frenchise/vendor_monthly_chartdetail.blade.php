@extends('layouts.admin')

@section('content')

<div class="right-side">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <!-- Starting of Dashboard data-table area -->
                <div class="section-padding add-product-1">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="add-product-box">
                                <div class="product__header">
                                    <div class="row reorder-xs">
                                        <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                            <div class="product-header-title">
                                                <h2>Monthly Chart Detail</h2>
                                                <p>Dashboard <i class="fa fa-angle-right" style="margin: 0 2px;"></i> Admin
                                                <i class="fa fa-angle-right" style="margin: 0 2px;"></i>Chart Detail</p>
                                            </div>
                                        </div>
                                        @include('includes.notification')
                                    </div>
                                </div>
                                <div>
                                    @include('includes.form-error')
                                    @include('includes.form-success')
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="table-responsive">
                                                <table id="" class="table table-striped table-hover table-bordered" cellspacing="0">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Oredr No</th>
                                                            <th>Amount</th>
                                                            <th>Sale Tax</th>
                                                            <th>Company</th>
                                    
                                                        </tr>
                                                    </thead>

                                                    <tbody>

                                                        @foreach($orders as $query)
                                                        <tr>
                                                            <td>{{$query->created_at->format('Y-m-d')}}</td>
                                                            <td>{{$query->order_number}}</td>
                                                            <td>{{$query->pay_amount}}</td>
                                                            <td>{{$query->sale_tax.'%'}}</td>
                                                            <td>{{$query->pay_amount-($query->sale_tax*$query->pay_amount/100)}}</td>
                                                            
                                                        
                                                        
    
                                                        </tr>
                                                        @endforeach



                                                        
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Ending of Dashboard data-table area -->
        </div>
    </div>
</div>
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center" id="myModalLabel">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p class="text-center">You are about to delete this Vendor. Everything will be deleted under this Vendor.</p>
                <p class="text-center">Do you want to proceed?</p>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm-delete2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center" id="myModalLabel">Accept Vendor</h4>
            </div>
            <div class="modal-body">
                <p class="text-center">You are about to accept this Vendor.</p>
                <p class="text-center">Do you want to proceed?</p>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-success btn-ok">Accept</a>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirm-delete1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-center" id="myModalLabel">Reject Vendor</h4>
            </div>
            <div class="modal-body">
                <p class="text-center">You are about to reject this Vendor.</p>
                <p class="text-center">Do you want to proceed?</p>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Reject</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script type="text/javascript">
    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });
    $('#confirm-delete1').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });
    $('#confirm-delete2').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });
</script>

@endsection