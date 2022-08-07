@extends('layouts.admin')
@section('content')
    <div class="right-side">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <!-- Starting of Dashboard add-product-1 area -->
                    <div class="section-padding add-product-1">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="add-product-box">
                                    <div class="product__header">
                                        <div class="row reorder-xs">
                                            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">
                                                <div class="product-header-title">
                                                    <h2>Edit Vendor <a href="{{ route('admin-vendor-index') }}" style="padding: 5px 12px;" class="btn add-back-btn"><i class="fa fa-arrow-left"></i> Back</a></h2>
                                                    <p>Dashboard <i class="fa fa-angle-right" style="margin: 0 2px;"></i> Vendors <i class="fa fa-angle-right" style="margin: 0 2px;"></i> Vendors List <i class="fa fa-angle-right" style="margin: 0 2px;"></i> Edit</p>
                                                </div>
                                            </div>
                                              @include('includes.notification')
                                        </div>   
                                    </div>
                                    <form class="form-horizontal" action="{{route('admin-vendor-edit',$user->id)}}" method="POST" enctype="multipart/form-data">
                                        {{ csrf_field() }}
                                        @include('includes.form-error')
                                        @include('includes.form-success')
                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="admin_current_photo">Current Photo *</label>
                                                <div class="col-sm-2">
                                                                                @if($user->is_provider == 1)
                                    <img style="width: 100%; height: auto;" src="{{ $user->photo ? $user->photo:asset('assets/images/noimage.png')}}" alt="profile image">
                                    @else
                                    <img  style="width: 100%; height: auto;" id="adminimg" src="{{ $user->photo ? asset('assets/images/'.$user->photo):asset('assets/images/noimage.png')}}" alt="profile image">
                                    @endif

                                                </div>
                                                @if($user->is_provider != 1)
                                                <div class="col-sm-4">
                                                    <input type="file" id="uploadFile" class="hidden" name="photo" value="">
                                                    <button  type="button" id="uploadTrigger" onclick="uploadclick()" class="btn btn-block add-product_btn adminImg-btn"><i class="fa fa-upload"></i> Change Photo</button>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="option">Select Vendor Type *</label>
                                                <div class="col-sm-6">
                                                    <select name="v_type" class="form-control">
                                                        <option value="">Select an option</option>
                                                        <option value="1"{{$user->wholesaler == 1 ? "selected":""}}>Wholesaler</option>
                                                        <option value="2"{{$user->wholesaler == 2 ? "selected":""}}>Unit</option>
                                                        <option value="3"{{$user->wholesaler == 3 ? "selected":""}}>Factory</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-sm-4" for="dash_lname">Email Address *</label>
                                                <div class="col-sm-6">
                                                    <input class="form-control" type="email" name="email" id="dash_lname" placeholder="Email Address" value="{{$user->email}}" disabled="">
                                                </div>
                                            </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="v1">Shop Name *</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="shop_name" id="v1" placeholder="Shop Name" value="{{$user->shop_name}}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="v2">Owner Name *</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="owner_name" id="v2" placeholder="Owner Name" value="{{$user->owner_name}}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="v3">Shop Mobile Number *</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="shop_number" id="v3" placeholder="Mobile Number" value="{{$user->shop_number}}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="v4">Address *</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="shop_address" id="v4" placeholder="Address" value="{{$user->shop_address}}" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="v5">Registration Number *<span>(This Field is Optional)</span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="reg_number" id="v5" placeholder="Registration Number" value="{{$user->reg_number}}" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="v5">Sale Tax *<span></span></label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" name="sale_tax" id="v5" placeholder="Sale Tax" value="{{$user->sale_tax}}" >
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4" for="v6">Message *<span>(This Field is Optional)</span></label>
                                            <div class="col-sm-6">
                                                <textarea class="form-control" id="v6" name="shop_message" placeholder="Message" rows="5">{{$user->shop_message}}</textarea>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="add-product-footer">
                                            <button name="addProduct_btn" type="submit" class="btn add-product_btn">Update</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Ending of Dashboard add-product-1 area -->
                </div>
            </div>
        </div>
    </div>
    <!-- Ending of Account Dashboard area -->
@endsection

@section('scripts')
    <script type="text/javascript">

        function uploadclick(){
            $("#uploadFile").click();
            $("#uploadFile").change(function(event) {
                readURL(this);
                $("#uploadTrigger").html($("#uploadFile").val());
            });

        }


        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#adminimg').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

    </script>

@endsection