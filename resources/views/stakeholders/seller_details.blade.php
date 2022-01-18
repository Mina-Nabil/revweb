@extends('layouts.app')


@section('content')

<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-5">
        <div class="card"> <img class="card-img" src="{{  (isset($seller->SLLR_IMGE)) ? $seller->getImageUrlAttribute() : asset('images/def-car.png')}}" alt="Card image">
        </div>

        <div class="card">
            <div class="card-body">
                <small class="text-muted">Seller Name </small>
                <h6>{{$seller->SLLR_NAME}}</h6>
                <small class="text-muted p-t-30 db">Email</small>
                <h6>{{$seller->SLLR_MAIL}} @if($seller->SLLR_MAIL_VRFD)
                    <i class="fas fa-check-circle" style="color:lightgreen"></i>
                    @else
                    <i class=" fas fa-exclamation-circle" style="color:red"></i>
                    @endif
                </h6>
                <small class="text-muted p-t-30 db">Phone1</small>
                <h6>{{$seller->SLLR_MOB1}} @if($seller->SLLR_MOB1_VRFD)
                    <i class="fas fa-check-circle" style="color:lightgreen"></i>
                    @else
                    <i class=" fas fa-exclamation-circle" style="color:red"></i>
                    @endif
                </h6>
                <small class="text-muted p-t-30 db">Phone2</small>
                <h6>{{$seller->SLLR_MOB2}} @if($seller->SLLR_MOB2_VRFD)
                    <i class="fas fa-check-circle" style="color:lightgreen"></i>
                    @else
                    <i class=" fas fa-exclamation-circle" style="color:red"></i>
                    @endif
                </h6>
                <small class="text-muted p-t-30 db">Number of offers</small>
                <h6>{{$seller->offers_count}}</h6>
                <small class="text-muted p-t-30 db">Since</small>
                <h6>{{$seller->offers_count}}</h6>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Cars Sold</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-5 text-success"><i class=" ti-money"></i></span>
                        <a href="javscript:void(0)" class="link display-5 ml-auto">{{number_format($seller->getCarsSoldCount())}}</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Cars Sold Price</h5>
                    <div class="d-flex m-t-30 m-b-20 no-block align-items-center">
                        <span class="display-5 text-success"><i class=" ti-book"></i></span>
                        <a href="javscript:void(0)" class="link display-5 ml-auto">{{number_format($seller->getCarsSoldPrice(),2)}}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#offers" role="tab">Offers</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#joinRequests" role="tab">Join Requests</a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">


                <div class="tab-pane active" id="offers" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Submitted Offers</h4>
                        <h6 class="card-subtitle">All Offers submitted by the Sellers</h6>
                        <div class="col-12">
                            <x-datatable id="sellerOffers" :title="$title ?? 'Offers'" :subtitle="$subTitle ?? ''" :cols="$offersCols" :items="$offersList" :atts="$offersAtts" :cardTitle="false" />
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="joinRequests" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">Join Requests</h4>
                        <h6 class="card-subtitle">All Join Requests between seller and showrooms</h6>
                        <div class="col-12">
                            <x-datatable id="sellerJoinRequests" :title="$title ?? 'Requests'" :subtitle="$subTitle ?? ''" :cols="$joinRequestsCols" :items="$joinRequestsList" :atts="$joinRequestsAtts"
                                :cardTitle="false" />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Column -->
</div>

@endsection