@extends('layouts.app')


@section('content')

<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-5">
        <div class="card"> <img class="card-img" src="{{  (isset($car->model->MODL_IMGE)) ? asset( 'storage/'. $car->model->MODL_IMGE ) : asset('images/def-car.png')}}" alt="Card image">
        </div>
        <div class="card"> <img class="card-img"
                src="{{  (isset($car->model->MODL_BRCH)) ? "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=http%3A%2F%2Findd.adobe.com%2Fview%2F{$car->model->MODL_BRCH}&choe=UTF-8"  : asset('images/def-car.png')}}"
                alt="Card image">
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Car Info</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#accessories" role="tab">Accessories</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#images" role="tab">Images</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <!--second tab-->
                <div class="tab-pane active" id="profile" role="tabpanel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Car Status</strong>
                                <br>
                                <p class="text-muted">{{$car->CAR_ACTV && $car->model->MODL_ACTV ? 'Active' : "Hidden"}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Model Status</strong>
                                <br>
                                <p class="text-muted">{{$car->model->MODL_ACTV ? 'Active' : "Hidden"}}</p>
                            </div>
                            <div title="Place in offer tab in the home page" class="col-md-3 col-xs-6 b-r"> <strong>Offer</strong>
                                <br>
                                <button id="offerLabel" class="label label-{{($car->CAR_OFFR) ? 'success' : 'danger'}}"
                                    onclick="toggleOffer()">{{($car->CAR_OFFR) ? 'In Offers' : 'Not in Offers'}}</button>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Trending</strong>
                                <br>
                                <button id="trendingLabel" class="label label-{{($car->CAR_TRND) ? 'success' : 'danger'}}"  onclick="toggleTrending()">{{($car->CAR_TRND) ? 'Trending' : 'Not Trending'}}</button>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Model Name</strong>
                                <br>
                                <p class="text-muted">{{$car->model->MODL_NAME}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Year</strong>
                                <br>
                                <p class="text-muted">{{$car->model->MODL_YEAR ?? ''}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Brand Name</strong>
                                <br>
                                <p class="text-muted">{{$car->model->brand->BRND_NAME ?? ''}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Type</strong>
                                <br>
                                <p class="text-muted">{{$car->model->type->TYPE_NAME ?? ''}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Category</strong>
                                <br>
                                <p class="text-muted">{{$car->CAR_CATG ?? ''}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Price</strong>
                                <br>
                                <p class="text-muted">{{number_format($car->CAR_PRCE ?? 0,0)}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Discount</strong>
                                <br>
                                <p class="text-muted">{{number_format($car->CAR_DISC ?? 0,0)}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Sort Value</strong>
                                <br>
                                <p class="text-muted">{{$car->CAR_VLUE ?? ''}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Added Date</strong>
                                <br>
                                <p class="text-muted">{{$car->created_at ?? ''}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Last Update Date</strong>
                                <br>
                                <p class="text-muted">{{$car->updated_at ?? ''}}</p>
                            </div>

                        </div>
                        <hr>
                        <div class=row>
                            <div class="col-md-12 col-xs-12 b-r ">
                                @if(isset($car->model->MODL_BRCH))
                                <iframe style="border: 1px solid #777; width:100% " src="https://indd.adobe.com/embed/{{$car->model->MODL_BRCH}}?startpage=1&allowFullscreen=false" height="371px"
                                    frameborder="0" allowfullscreen=""></iframe>
                                @else
                                <p class="text-muted">Interactive Brochure Area</p>
                                @endif

                            </div>
                        </div>
                        <hr>
                        <div class=row>
                            <div class="col-12 b-r">
                                <strong>Car Overview </strong>
                                <p class="text-muted"><strong>{{$car->CAR_TTL1 ?? ''}}</strong></p>
                                <p class="text-muted">{{$car->CAR_PRG1 ?? ''}}</p>
                                <p class="text-muted"><strong>{{$car->CAR_TTL2 ?? ''}}</strong></p>
                                <p class="text-muted">{{$car->CAR_PRG2 ?? ''}}</p>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>

                <div class="tab-pane" id="accessories" role="tabpanel">
                    <div class="card-body">
                        <div>
                            <div class=row>
                                <div class=col-8>
                                    <h4 class="card-title">Accessories</h4>
                                </div>
                                <div class=col-4>
                                    <button type="button" data-toggle="modal" data-target="#accessories-modal" class="btn btn-info d-none d-lg-block m-l-15">
                                        <i class="fa fa-plus-circle"></i> Import Accessories
                                    </button>
                                </div>

                            </div>
                            <div class=col>
                                <div class="table-responsive m-t-40">
                                    <table class="table color-bordered-table table-striped full-color-table full-primary-table hover-table" data-display-length='-1' data-order="[]">
                                        <thead>
                                            <th>Accessory</th>
                                            <th>Available</th>
                                            <th>Value</th>
                                            <th>Actions</th>
                                        </thead>
                                        <tbody>
                                            @foreach ($accessories as $accessID => $accessory)
                                            <tr>
                                                <td> {{$accessory['ACSR_NAME']}} </td>
                                                <td>
                                                    @if ($accessory['isAvailable'])
                                                    <label id="accssLabel{{$accessID}}" class="label label-success">Available</label>
                                                    @else
                                                    <label id="accssLabel{{$accessID}}" class="label label-danger">Unavailable</label>
                                                    @endif
                                                </td>
                                                <td id="valueCell{{$accessID}}"> {{$accessory['ACCR_VLUE'] ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select </button>
                                                        <div class="dropdown-menu" id="group{{$accessID}}">
                                                            @if($accessory['isAvailable'])
                                                            <button class="dropdown-item" onclick="unlinkAccessory('{{$accessID}}')">Unlink!</button>
                                                            @endif
                                                            <button class="open-AccessoryEditDialog  dropdown-item" data-toggle="modal" data-id="{{$accessID}}%%{{$accessory['ACCR_VLUE']??''}}"
                                                                data-target="#setAccessory">
                                                                {{$accessory['isAvailable'] ? 'Set Value' : 'Link Accessory'}}
                                                            </button>
                                                        </div>

                                                    </div>
                                                </td>
                                            <tr>
                                                @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane" id="images" role="tabpanel">
                    <div class="card-body">
                        <div id="carouselExampleIndicators2" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <?php $i=0; ?>
                                @foreach($car->images as $image)
                                <li data-target="#carouselExampleIndicators2" data-slide-to="{{$i}}" {{($i==0) ? 'class="active"' : ''}}></li>
                                <?php $i++; ?>
                                @endforeach
                            </ol>
                            <div class="carousel-inner" role="listbox">
                                <?php $i=0; ?>
                                @foreach($car->images as $image)
                                <div class="carousel-item {{($i==0) ? 'active' : ''}}">
                                    <img class="img-fluid" src="{{ asset( 'storage/'. $image->CIMG_URL ) }} "
                                        style="max-height:560px; max-width:900px; display: block;  margin-left: auto;  margin-right: auto;">
                                </div>
                                <?php $i++; ?>
                                @endforeach
                            </div>
                            <a class="carousel-control-prev" href="#carouselExampleIndicators2" role="button" data-slide="prev" style="background-color:#DCDCDC">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleIndicators2" role="button" data-slide="next" style="background-color:#DCDCDC">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                        <hr>
                        <h4 class="card-title">Add New Car Image</h4>
                        <form class="form pt-3" method="post" action="{{ url($imageFormURL) }}" enctype="multipart/form-data">
                            @csrf
                            <input type=hidden name=carID value="{{(isset($car)) ? $car->id : ''}}">
                            <div class="form-group">
                                <label>Sort Value*</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon11"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control" placeholder="Example: 900" name=value value="{{ (isset($car)) ? $car->CAR_VLUE : old('value') ?? 500}}" required>
                                </div>
                                <small class="text-muted">Default is 500, the image with the higher value appears before other image</small>
                                <small class="text-danger">{{$errors->first('value')}}</small>
                            </div>
                            <div class="form-group">
                                <label for="input-file-now-custom-1">New Photo</label>
                                <div class="input-group mb-3">
                                    <input type="file" id="input-file-now-custom-1" name=photo class="dropify" data-default-file="{{ old('photo') }}" />
                                </div>
                                <small class="text-muted">Optimum Resolution is 900 * 560</small>
                            </div>

                            <button type="submit" class="btn btn-success mr-2">Submit</button>
                            @if($isCancel)
                            <a href="{{url($homeURL) }}" class="btn btn-dark">Cancel</a>
                            @endif
                        </form>
                    </div>
                    <hr>
                    <div>
                        <div class=col>
                            <h4 class="card-title">Car Images</h4>
                            <div class="table-responsive m-t-40">
                                <table class="table color-bordered-table table-striped full-color-table full-primary-table hover-table" data-display-length='-1' data-order="[]">
                                    <thead>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Url</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($car->images as $image)
                                        <tr>
                                            <td id="imageValue{{$image->id}}">{{$image->CIMG_VLUE}}</td>
                                            <td> <img src="{{ asset( 'storage/'. $image->CIMG_URL ) }} " width="60px"> </td>
                                            <td><a target="_blank" href="{{ asset( 'storage/'. $image->CIMG_URL ) }}">
                                                    {{(strlen($image->CIMG_URL) < 25) ? $image->CIMG_URL : substr($image->CIMG_URL, 0, 25).'..' }}
                                                </a></td>
                                            <td>
                                                <div class=" row justify-content-center ">
                                                    <a href="javascript:void(0)" class="openEditImage" data-toggle="modal" data-id="{{$image->id}}" data-target="#edit-image">
                                                        <img src="{{ asset('images/edit.png') }}" width=25 height=25>
                                                    </a>

                                                    <a href="javascript:void(0);" onclick="deleteImage({{$image->id}})">
                                                        <img src="{{ asset('images/del.png') }}" width=25 height=25>
                                                    </a>
                                                </div>
                                            </td>
                                        <tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="settings" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <form class="form pt-3" method="post" action="{{ $formURL }}" enctype="multipart/form-data">
                                @csrf
                                @isset($car)
                                <input type=hidden name=id value="{{(isset($car)) ? $car->id : ''}}">
                                @endisset

                                <h4 class="card-title">Car Profile Status</h4>

                                <div class="form-group bt-switch">
                                    <div class="col-md-5 m-b-15">
                                        <h4 class="card-title">Active</h4>
                                        <input type="checkbox" data-size="large" {{(isset($car) && $car->CAR_ACTV) ? 'checked' : ''}} data-on-color="success" data-off-color="danger"
                                            data-on-text="Active" data-off-text="Hidden" name="isActive">
                                    </div>
                                    <small class="text-muted">Use this option when the car is ready for publishing</small>
                                </div>
                                <hr>
                                <h4 class="card-title">Main Car Info.</h4>

                                <div class="form-group">
                                    <label>Model*</label>
                                    <div class="input-group mb-3">
                                        <select name=model class="select2 form-control custom-select" style="width: 100%; height:36px;" required>
                                            <option value="" disabled selected>Pick From Models</option>
                                            @foreach($models as $model)
                                            <option value="{{ $model->id }}" @if(isset($car) && $model->id == $car->CAR_MODL_ID)
                                                selected
                                                @elseif($model->id == old('model'))
                                                selected
                                                @endif
                                                >{{$model->brand->BRND_NAME}} {{$model->brand->BRND_ACTV ? '' : '(In-Active) '}}: {{$model->MODL_NAME}} - {{$model->MODL_YEAR}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-danger">{{$errors->first('brand')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Category*</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-car"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Category Name - Example: Allure+" name=category
                                            value="{{ (isset($car)) ? $car->CAR_CATG : old('category')}}" required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('category')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Price*</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-dollar-sign"></i></span>
                                        </div>
                                        <input type="number" class="form-control" placeholder="Car Price" name=price value="{{ (isset($car)) ? $car->CAR_PRCE : old('price')}}" required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('price')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Discount</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="far fa-calendar"></i></span>
                                        </div>
                                        <input type="number" class="form-control" placeholder="Enter discount amount" name=disc value="{{ (isset($car)) ? $car->CAR_DISC : old('disc')}}">
                                    </div>
                                    <small class="text-muted">Use only to show <span style="text-decoration:line-through">old price</span> and <strong>new price</strong></small><br>
                                    <small class="text-danger">{{$errors->first('disc')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Sorting Value</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-list"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name=sort placeholder="Default is 500" value="{{ (isset($car)) ? $car->CAR_VLUE : old('sort')}}">
                                    </div>
                                    <small class="text-muted">Higher value means higher priority, cars with higher value will appear before others on search pages</small><br>
                                    <small class="text-danger">{{$errors->first('sort')}}</small>
                                </div>
                                <div class=row>
                                    <div class=col-8>
                                        <h4 class="card-title">Car Specifications</h4>
                                    </div>
                                    <div class=col-4>
                                        <button type="button" data-toggle="modal" data-target="#cars-modal" class="btn btn-info d-none d-lg-block m-l-15">
                                            <i class="fa fa-plus-circle"></i> Import Car Profile
                                        </button>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Engine CC</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-database"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name=cc id=cc placeholder="Enter Engine CC Specs, Example: 1500 Turbo"
                                            value="{{ (isset($car)) ? $car->CAR_ENCC : old('cc')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('cc')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Horse Power</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-horse"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name=hpwr id=hpwr placeholder="Enter Horse Power in Hp@rpm, Example: 129@6000"
                                            value="{{ (isset($car)) ? $car->CAR_HPWR : old('hpwr')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('hpwr')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Engine Torque</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-cog"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name=torq id=torq placeholder="Enter torque in Nm@rpm, Example: 230@1750"
                                            value="{{ (isset($car)) ? $car->CAR_TORQ : old('torq')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('torq')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Transmission info</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-filter"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name=trns id=trns placeholder="Example: 6 Speed A/T" value="{{ (isset($car)) ? $car->CAR_TRNS : old('trns')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('trns')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">0-100 Acceleration</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-tachometer-alt"></i></span>
                                        </div>
                                        <input type="number" step=0.1 class="form-control" name=acc id=acc placeholder="Enter Car Acceleration in seconds, Example: 9.4"
                                            value="{{ (isset($car)) ? $car->CAR_ACC : old('acc')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('acc')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Top Speed</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-flag-checkered"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name=speed id=speed placeholder="Car Top Speed in Km/h, Example: 220"
                                            value="{{ (isset($car)) ? $car->CAR_TPSP : old('speed')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('speed')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Car Height</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-long-arrow-alt-up"></i></span>
                                        </div>
                                        <input type="number" step=.1 class="form-control" name=height id=height placeholder="Car height from the ground in cm, Example: 174.4"
                                            value="{{ (isset($car)) ? $car->CAR_HEIT : old('height')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('height')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Rims Measure</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-life-ring"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name=rims id=rims placeholder="Rims Tyre measure, Example: 16"
                                            value="{{ (isset($car)) ? $car->CAR_RIMS : old('rims')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('rims')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Gas Tank Capicity</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-flask"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name=tank id=tank placeholder="Gas Tank Capicity in Litres, Example: 45"
                                            value="{{ (isset($car)) ? $car->CAR_TRNK : old('tank')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('tank')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Seats</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-couch"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name=seat id=seat placeholder="Car Seats, Example: 5" value="{{ (isset($car)) ? $car->CAR_SEAT : old('seat')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('seat')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Dimensions</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-car"></i></span>
                                        </div>
                                        <input type="number" class="form-control" name=dimn id=dimn placeholder="Car Dimensions" value="{{ (isset($car)) ? $car->CAR_DIMN : old('dimn')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('dimn')}}</small>
                                </div>

                                <hr>
                                <h4 class="card-title">Car Overview</h4>


                                <div class="form-group">
                                    <label for="exampleInputEmail1">Title 1*</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name=title1 placeholder="First Paragraph title" value="{{ (isset($car)) ? $car->CAR_TTL1 : old('title1')}}">
                                    </div>
                                    <small class="text-muted">First Car Title in overview section, shown on the car details page</small><br>
                                    <small class="text-danger">{{$errors->first('title1')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Paragraph 1*</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" name=prgp1 rows="3">{{(isset($car)) ? $car->CAR_PRG1 : old('prgp1')}}</textarea>
                                    </div>
                                    <small class="text-danger">{{$errors->first('prgp1')}}</small>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Title 2</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" name=title2 placeholder="Second Paragraph title" value="{{ (isset($car)) ? $car->CAR_TTL2 : old('title2')}}">
                                    </div>
                                    <small class="text-muted">Second Overview paragraph shown on the car details page, leave empty if not needed</small><br>

                                    <small class="text-danger">{{$errors->first('title2')}}</small>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Paragraph 2</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" name=prgp2 rows="3">{{(isset($car)) ? $car->CAR_PRG2 : old('prgp2')}}</textarea>
                                    </div>
                                    <small class="text-muted">Required if second title is not empty</small><br>
                                    <small class="text-danger">{{$errors->first('prgp2')}}</small>
                                </div>


                                <button type="submit" class="btn btn-success mr-2">Save</button>
                                @if($isCancel)
                                <a href="{{url($homeURL) }}" class="btn btn-dark">Cancel</a>
                                @endif
                            </form>
                            <hr>
                            <h4 class="card-title">Delete Car</h4>
                            <button type="button" onclick="confirmAndGoTo('{{url('users/delete/'.$car->model->id )}}', 'delete this Car and all its data ?')"
                                class="btn btn-danger mr-2">Delete
                                All Car Data</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
<div id="setAccessory" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Link Accessory</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{ $linkAccessoryURL }}" method=post>
                @csrf
                <div class="modal-body">
                    <input type=hidden name=accessID id=accessIDField value="">

                    <div class="form-group col-md-12 m-t-0">
                        <h5>Value</h5>
                        <input type="text" class="form-control form-control-line" name=value id=accessValue>
                        <small class="text-muted">(optional)Use this option if you want to set a value to the accessory</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <button type="button" onclick="submitAccessoryForm()" data-dismiss="modal" class="btn btn-warning waves-effect waves-light">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="cars-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Load Car Profile</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group col-md-12 m-t-0">
                    <h5>Cars</h5>
                    <select class="select2 form-control" style="width:100%" id=carID>
                        <?php foreach ($cars as $carItem) { ?>
                        <option value="{{$car->id}}">
                            {{$carItem->model->brand->BRND_NAME}}: {{$carItem->model->MODL_NAME}} {{$carItem->CAR_CATG}} {{$carItem->model->MODL_YEAR}}
                        </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-lg-3">
                    <div class="form-group col-12 m-t-10">
                        <button onclick="importCarProfile()" class="btn btn-success waves-effect waves-light m-r-20">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="accessories-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Load Accessories</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form class="form pt-3" method="post" action="{{ url($loadAccessoriesURL) }}" enctype="multipart/form-data">
                    @csrf
                    <input type=hidden name=id value="{{(isset($car)) ? $car->id : ''}}">
                    <div class="form-group col-md-12 m-t-0">
                        <h5>Cars</h5>
                        <select class="select2 form-control" style="width:100%" name=carID>
                            <?php foreach ($cars as $carItem) { ?>
                            <option value="{{$carItem->id}}">
                                {{$carItem->model->brand->BRND_NAME}}: {{$carItem->model->MODL_NAME}} {{$carItem->CAR_CATG}} {{$carItem->model->MODL_YEAR}}
                            </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group col-12 m-t-10">
                            <button type="submit" class="btn btn-success waves-effect waves-light m-r-20">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="edit-image" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Image</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type=hidden name=id id=modalImageID value="">

                <div class="form-group col-md-12 m-t-0">
                    <h5>Value</h5>
                    <input type="text" class="form-control form-control-line" name=value id=sortModal>
                </div>

                <div class="col-lg-3">
                    <div class="form-group col-12 m-t-10">
                        <button onclick="updateImageInfo()" class="btn btn-success waves-effect waves-light m-r-20">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleOffer(){
        var http = new XMLHttpRequest();
        var url = "{{$toggleOffer}}" ;
        var ret = false;
        var formdata = new FormData();

        formdata.append('_token','{{ csrf_token() }}');
        formdata.append('carID', '{{ $car->id }}');

        http.open('POST', url);

        http.onreadystatechange = function(ret) {
            if (this.readyState == 4 && this.status == 200 && IsNumeric(this.responseText)) {
                if(this.responseText == 1){
                    setOffer(true)
                } else {
                    setOffer(false)
                }
            }
        };
        http.send(formdata);
        return ret;
    }

    function toggleTrending(){
        var http = new XMLHttpRequest();
        var url = "{{$toggleTrending}}" ;
        var ret = false;
        var formdata = new FormData();

        formdata.append('_token','{{ csrf_token() }}');
        formdata.append('carID', '{{ $car->id }}');

        http.open('POST', url);

        http.onreadystatechange = function(ret) {
            if (this.readyState == 4 && this.status == 200 && IsNumeric(this.responseText)) {
                if(this.responseText == 1){
                    setTrending(true)
                } else {
                    setTrending(false)
                }
            }
        };
        http.send(formdata);
        return ret;
    }

    function setOffer(state){
        if(state){
            $('#offerLabel').attr('class', 'label label-success');
            $('#offerLabel').html("In Offers")
        } else {
            $('#offerLabel').attr('class', 'label label-danger');
            $('#offerLabel').html("Not In Offers")
        }
    }

    function setTrending(state){
        if(state){
            $('#trendingLabel').attr('class', 'label label-success');
            $('#trendingLabel').html("Trending")
        } else {
            $('#trendingLabel').attr('class', 'label label-danger');
            $('#trendingLabel').html("Not Trending")
        }
    }


    function deleteImage(id){
        var http = new XMLHttpRequest();
        var url = "{{$delImageUrl}}" + '/' +  id;
        http.open('GET', url, true);
        //Send the proper header information along with the request
        http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if(this.responseText=='1')
            Swal.fire({
                title: "Deleted!",
                text: "Image will disappear after reload..",
                icon: "success"
            })
            else 
            Swal.fire({
                title: "Error!",
                text: "Something went wrong..",
                icon: "error"
            })
        } else {
            Swal.fire({
                title: "Error!",
                text: "Something went wrong.. Please refresh",
                icon: "error"
            })
        }
    };

    http.send();
}
    function submitAccessoryForm(){
        let http = new XMLHttpRequest();
        let url = "{{$linkAccessoryURL}}" ;
        var formdata = new FormData();
        formdata.append('carID',{{$car->id}});
        var accessIDVal = document.getElementById('accessIDField').value;
        formdata.append('accessID',accessIDVal);
        formdata.append('_token','{{ csrf_token() }}');
   
        var accessVal = document.getElementById('accessValue').value;
        formdata.append('value',accessVal);
  
        http.open('POST', url, true);
        //Send the proper header information along with the request
        http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if(this.responseText=='1'){
                Swal.fire({
                title: "Success!",
                text: "Accessory successfully updated",
                icon: "success"
            })
            accessLabel = document.getElementById('accssLabel' + accessIDVal)
            accessLabel.innerHTML = 'Available'
            accessLabel.className = 'label label-success'
            accessCell = document.getElementById('valueCell' + accessIDVal)
            accessCell.innerHTML = accessVal.length>0 ? accessVal : 'N/A'
            adjustMenu(accessIDVal, true, accessVal.length>0 ? accessVal : '')
            } else {
                Swal.fire({
                title: "No Change!",
                text: "Something went wrong.. Please refresh",
                icon: "warning"
            })
            }

        } else {
            Swal.fire({
                title: "Error!",
                text: "Something went wrong.. Please refresh",
                icon: "error"
            })
        }
    };
    http.send(formdata, true);
    }

    function unlinkAccessory(id){
            let http = new XMLHttpRequest();
            let url = "{{$unlinkAccessoryURL}}" + '/' + {{$car->id}} + '/' +  id;
            http.open('GET', url, true);
            //Send the proper header information along with the request
            http.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if(this.responseText=='1'){
                    accessLabel = document.getElementById('accssLabel' + id)
                    accessLabel.innerHTML = 'Unavailable'
                    accessLabel.className = 'label label-danger'
                    accessCell = document.getElementById('valueCell' + id)
                    accessCell.innerHTML = 'N/A'
                    adjustMenu(id, false, '')
                    Swal.fire({
                        title: "Removed!",
                        text: "Accessory not linked with the care",
                        icon: "success"
                    })
                }
                else 
                Swal.fire({
                    title: "Error!",
                    text: "Something went wrong.. Please refresh",
                    icon: "error"
                })
            
            } else {
            Swal.fire({
                title: "Error!",
                text: "Something went wrong.. Please refresh",
                icon: "error"
            })
        }
        };

        http.send();
    }

    function adjustMenu(id, linked, value){
        accessMenu = document.getElementById('group' + id);
        if(linked){
            accessMenu.innerHTML = `  <button class='dropdown-item' onclick='unlinkAccessory(${id})'>Unlink!</button>\
                                     <button class='open-AccessoryEditDialog  dropdown-item' data-toggle='modal' data-id=${id}%%${value}\
                                        data-target='#setAccessory'>Set Value</button>`;
        } else {
            accessMenu.innerHTML =  `  <button class='open-AccessoryEditDialog  dropdown-item' data-toggle='modal' data-id=${id}%%${value}\
                                        data-target='#setAccessory'>Link Accessory</button>`;
        }
    }
    function IsValidJSONString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
            return true;
    }
  
    function importCarProfile(){

        let http = new XMLHttpRequest();
        let url = '{{$loadCarURL}}';
        http.open('POST', url, true);
        //Send the proper header information along with the request
        //http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        var formdata = new FormData();
        formdata.append('_token','{{ csrf_token() }}');
        formdata.append('carID', document.getElementById('carID').value);

        http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {

            if (IsValidJSONString(this.responseText)) {
            var json = JSON.parse(this.responseText);

            Swal.fire({
                title: "Success!",
                text: "Car Specs loaded",
                icon: "success"
            })
            document.getElementById("cc").value = json.CAR_ENCC;
            document.getElementById("hpwr").value = json.CAR_HPWR;
            document.getElementById("torq").value = json.CAR_TORQ;
            document.getElementById("trns").value = json.CAR_TRNS;
            document.getElementById("acc").value = json.CAR_ACC;
            document.getElementById("speed").value = json.CAR_TPSP;
            document.getElementById("height").value = json.CAR_HEIT;
            document.getElementById("tank").value = json.CAR_TRNK;
            document.getElementById("rims").value = json.CAR_RIMS;
            document.getElementById("seat").value = json.CAR_SEAT;
            document.getElementById("dimn").value = json.CAR_DIMN;
            } else {
                Swal.fire({
                title: "Error!",
                text: "Loading Profile Failed ",
                icon: "error"
            })
            }
        };
        }

        http.send(formdata, true);
}


function updateImageInfo(){
        let http = new XMLHttpRequest();
        let url = "{{$updateImageInfoURL}}" ;

        var imageID = $('#modalImageID').val();
        var sort = $('#sortModal').val();

        var formdata = new FormData();
        formdata.append('_token','{{ csrf_token() }}');
        formdata.append('id',imageID);
        formdata.append('value',sort);
   
        http.open('POST', url, true);
        //Send the proper header information along with the request
        http.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if(this.responseText=='1'){
                Swal.fire({
                title: "Success!",
                text: "Image successfully updated",
                icon: "success"
            })
            $('#imageValue'+imageID).html(sort);
            } else {
                Swal.fire({
                title: "No Change!",
                text: "Something went wrong.. Please refresh",
                icon: "warning"
            })
            }

        } else {
            Swal.fire({
                title: "Error!",
                text: "Something went wrong.. Please refresh",
                icon: "error"
            })
        }
    };
    http.send(formdata, true);
    }
</script>
@endsection

@section('js_content')
<script>
    $(document).on("click", ".open-AccessoryEditDialog", function () {
  
        var accessData = $(this).data('id');
        var accessArr = accessData.split('%%');
        $(".modal-body #accessIDField").val( accessArr[0] );
        $(".modal-body #accessValue").val( accessArr[1] );

    });


    $('.openEditImage').on("click", function () {
  
        var id = $(this).data('id');
        var sort = $('#imageValue'+id).html();

        $(".modal-body #sortModal").val(sort);
        $(".modal-body #modalImageID").val(id);

    });
</script>
@endsection