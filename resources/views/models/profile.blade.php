@extends('layouts.app')


@section('content')

<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-5">
        <div class="card"> <img class="card-img" src="{{  (isset($model->MODL_IMGE)) ? $model->getImageUrlAttribute() : asset('images/def-car.png')}}" alt="Card image">
        </div>

    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-8 col-xlg-9 col-md-7">
        <div class="card">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs profile-tab" role="tablist">
                <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#profile" role="tab">Model Info</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#cars" role="tab">Cars</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#colors" role="tab">Colors</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#images" role="tab">Images</a> </li>
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#settings" role="tab">Settings</a> </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <!--second tab-->
                <div class="tab-pane active" id="profile" role="tabpanel">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Model Name</strong>
                                <br>
                                <p class="text-muted">{{$model->MODL_NAME}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Year</strong>
                                <br>
                                <p class="text-muted">{{$model->MODL_YEAR ?? ''}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Brand Name</strong>
                                <br>
                                <p class="text-muted">{{$model->brand->BRND_NAME ?? ''}}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Type</strong>
                                <br>
                                <p class="text-muted">{{$model->type->TYPE_NAME ?? ''}}</p>
                            </div>
                            <div class="col-md-6 col-xs-6 b-r"> <strong>Status</strong>
                                <br>
                                <p class="text-muted">{{$model->MODL_ACTV ? 'Active' : "Hidden"}}</p>
                            </div>
                            <div class="col-md-6 col-xs-6"> <strong>Main</strong>
                                <br>
                                <p class="text-muted">{{$model->MODL_MAIN ? 'Shown in home page & car menus' : 'Shown in car menus'}}</p>
                            </div>
                        </div>
                        <hr>
                        <div class=row>
                            <div class="col-12 b-r">
                                <strong>Option PDF Brochure</strong>
                                @isset($model->MODL_BRCH)
                                <embed class="m-t-10" src="{{$model->pdf_url}}" width="100%" height="375px">
                                @endisset
                            </div>
                        </div>
                        <hr>
                        <div class=row>
                            <div class="col-12 b-r">
                                <strong>Overview</strong>
                                <p class="text-muted">{{$model->MODL_OVRV ?? ''}}</p>
                            </div>
                        </div>
                        <hr>
                    </div>
                </div>




                <div class="tab-pane" id="cars" role="tabpanel">
                    <div class="card-body">
                        <h4 class="card-title">All Categories</h4>
                        <hr>
                        <x-datatable id="myTable" :title="$title" :subtitle="$subTitle" :cols="$cols" :items="$items" :atts="$atts" />
                    </div>
                </div>

                <div class="tab-pane" id="colors" role="tabpanel">
                    <div class="card-body">
                        <div id="carouselExampleIndicators2" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <?php $i=0; ?>
                                @foreach($model->colors as $image)
                                @isset($image->image_url)
                                <li data-target="#carouselExampleIndicators2" data-slide-to="{{$i}}" {{($i==0) ? 'class="active"' : '' }}></li>
                                <?php $i++; ?>
                                @endisset
                                @endforeach
                            </ol>
                            <div class="carousel-inner" role="listbox">
                                <?php $i=0; ?>
                                @foreach($model->colors as $image)
                                @isset($image->image_url)
                                <div class="carousel-item {{($i==0) ? 'active' : ''}}">
                                    <img class="img-fluid" src="{{ $image->image_url }} " style="max-height:560px; max-width:900px; display: block;  margin-left: auto;  margin-right: auto;">
                                </div>
                                <?php $i++; ?>
                                @endisset
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
                        <h4 class="card-title">Add New Model Color</h4>
                        <form class="form pt-3" method="post" action="{{ $colorFormURL }}" enctype="multipart/form-data">
                            @csrf
                            <input type=hidden name=modelID value="{{(isset($model)) ? $model->id : ''}}">
                            <div class=row>
                                <div class="col-6 form-group">
                                    <label>Color Name*</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Example: Baby Blue" name=name value="{{old('name')}}" required>
                                    </div>
                                    <small class="text-muted">Model Color name as stated in Cars brochures</small>
                                    <small class="text-danger">{{$errors->first('name')}}</small>
                                </div>

                                <div class="col-6 form-group">
                                    <label>Arabic Color Name</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Example: ازرق زهري" name=arbcName value="{{old('arbcName')}}">
                                    </div>
                                    <small class="text-muted">Model Color name as stated in Cars brochures</small>
                                    <small class="text-danger">{{$errors->first('name')}}</small>
                                </div>
                            </div>

                            <div class=row>
                                <div class="col-4 form-group">
                                    <label>Red Value</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                        </div>
                                        <input type="number" max=255 min=0 step="1" class="form-control" name=red value="{{old('red')}}" required>
                                    </div>
                                    <small class="text-muted">Red Value under RGB code</small>
                                    <small class="text-danger">{{$errors->first('red')}}</small>
                                </div>

                                <div class="col-4 form-group">
                                    <label>Green Value</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                        </div>
                                        <input type="number" max=256 min=0 step="1" class="form-control" name=green value="{{old('green')}}" required>
                                    </div>
                                    <small class="text-muted">Green Value under RGB code</small>
                                    <small class="text-danger">{{$errors->first('green')}}</small>
                                </div>

                                <div class="col-4 form-group">
                                    <label>Blue Value</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                        </div>
                                        <input type="number" max=255 min=0 step="1" class="form-control" name=blue value="{{old('blue')}}" required>
                                    </div>
                                    <small class="text-muted">Blue Value under RGB code</small>
                                    <small class="text-danger">{{$errors->first('blue')}}</small>
                                </div>
                            </div>
                            <div class=row>
                                <div class="col-6 form-group">
                                    <label>Hex Value</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Example: FF650C" name=hex value="{{old('hex')}}">
                                    </div>
                                    <small class="text-muted">Model Color hex code (optional)</small>
                                    <small class="text-danger">{{$errors->first('hex')}}</small>
                                </div>

                                <div class="col-6 form-group">
                                    <label>Alpha Value</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                        </div>
                                        <input type="number" max=100 min=0 step="1" class="form-control" name=alpha value="{{old('alpha') ?? 100}}" required>
                                    </div>
                                    <small class="text-muted">Model Color Alpha Value (0-100) - Default 100</small>
                                    <small class="text-danger">{{$errors->first('alpha')}}</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="input-file-now-custom-1">New Photo</label>
                                <div class="input-group mb-3">
                                    <input type="file" id="input-file-now-custom-1" name=photo class="dropify" data-default-file="{{ old('photo') }}" data-max-file-size="2M" />
                                </div>
                                <small class="text-muted">Optimum Resolution is 148 * 100</small>
                            </div>
                            <button type="submit" class="btn btn-success mr-2">Submit</button>
                        </form>
                    </div>
                    <hr>
                    <div>
                        <div class=col>
                            <h4 class="card-title">Color Images</h4>
                            <div class="table-responsive m-t-40">
                                <table class="table color-bordered-table table-striped full-color-table full-primary-table hover-table" data-display-length='-1' data-order="[]">
                                    <thead>
                                        <th>RGB</th>
                                        <th>Color</th>
                                        <th>Image</th>
                                        <th>Url</th>
                                        <th>Actions</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($model->colors as $color)
                                        <div style="display: none">
                                            <div id="colorName{{$color->id}}">{{$color->COLR_NAME}}</div>
                                            <div id="colorArbcName{{$color->id}}">{{$color->COLR_ARBC_NAME}}</div>
                                            <div id="colorHex{{$color->id}}">{{$color->COLR_HEX}}</div>
                                            <div id="colorRed{{$color->id}}">{{$color->COLR_RED}}</div>
                                            <div id="colorBlue{{$color->id}}">{{$color->COLR_BLUE}}</div>
                                            <div id="colorGreen{{$color->id}}">{{$color->COLR_GREN}}</div>
                                            <div id="colorAlpha{{$color->id}}">{{$color->COLR_ALPH}}</div>
                                            <div id="colorURL{{$color->id}}">{{$color->COLR_IMGE}}</div>
                                        </div>
                                        <tr>
                                            <td>rgb({{$color->COLR_RED}}, {{$color->COLR_GREN}}, {{$color->COLR_BLUE}})</td>
                                            <td>{{$color->COLR_NAME}}-{{$color->COLR_ARBC_NAME}}</td>
                                            <td>
                                                @isset($color->COLR_IMGE)
                                                <img src="{{ $color->color_url }} " width="60px">
                                                @endisset
                                            </td>
                                            <td><a target="_blank" href="{{  $color->image_url }}">
                                                    {{(strlen($color->COLR_IMGE) < 25) ? $color->COLR_IMGE : substr($color->COLR_IMGE, 0, 25).'..' }}
                                                </a></td>
                                            <td>
                                                <div class=" row justify-content-center ">
                                                    <a href="javascript:void(0)" onclick="loadColorEditModal({{$color->id}})" data-toggle="modal" data-id="{{$color->id}}" data-target="#edit-color">
                                                        <img src="{{ asset('images/edit.png') }}" width=25 height=25>
                                                    </a>
                                                    <a href="javascript:void(0);" onclick="deleteImage({{$color->id}})">
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

                <div class="tab-pane" id="images" role="tabpanel">
                    <div class="card-body">
                        <div id="carouselExampleIndicators2" class="carousel slide" data-ride="carousel">
                            <ol class="carousel-indicators">
                                <?php $i=0; ?>
                                @foreach($model->images as $image)
                                <li data-target="#carouselExampleIndicators2" data-slide-to="{{$i}}" {{($i==0) ? 'class="active"' : '' }}></li>
                                <?php $i++; ?>
                                @endforeach
                            </ol>
                            <div class="carousel-inner" role="listbox">
                                <?php $i=0; ?>
                                @foreach($model->images as $image)
                                <div style="display: none">
                                    <div id="imageSort{{$image->id}}">{{$image->MOIM_SORT}}</div>
                                    <div id="imageURL{{$image->id}}">{{$image->MOIM_URL}}</div>
                                </div>
                                <div class="carousel-item {{($i==0) ? 'active' : ''}}">
                                    <img class="img-fluid" src="{{ $image->image_url }} " style="max-height:560px; max-width:900px; display: block;  margin-left: auto;  margin-right: auto;">
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
                        <h4 class="card-title">Add New Model Image</h4>
                        <form class="form pt-3" method="post" action="{{ url($imageFormURL) }}" enctype="multipart/form-data">
                            @csrf
                            <input type=hidden name=modelID value="{{(isset($model)) ? $model->id : ''}}">
                            <div class="form-group">
                                <label>Sort Value*</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon11"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="number" class="form-control" placeholder="Example: 900" name=sort value="{{old('sort')??500}}" required>
                                </div>
                                <small class="text-muted">Default is 500, the image with the higher value appears before other image</small>
                                <small class="text-danger">{{$errors->first('sort')}}</small>
                            </div>
                            <div class="form-group">
                                <label for="input-file-now-custom-1">New Photo</label>
                                <div class="input-group mb-3">
                                    <input type="file" id="input-file-now-custom-1" name=photo class="dropify" data-default-file="{{ old('photo') }}" />
                                </div>
                                <small class="text-muted">Optimum Resolution is 300 * 150</small>
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
                            <h4 class="card-title">Model Images</h4>
                            <div class="table-responsive m-t-40">
                                <table class="table color-bordered-table table-striped full-color-table full-primary-table hover-table" data-display-length='-1' data-order="[]">
                                    <thead>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Url</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($model->images as $image)
                                        <tr>
                                            <td id="imageValue{{$image->id}}">{{$image->MOIM_VLUE}}</td>
                                            <td> <img src="{{ $image->image_url }} " width="60px"> </td>
                                            <td><a target="_blank" href="{{ $image->image_url }}">
                                                    {{(strlen($image->MOIM_URL) < 25) ? $image->image_url : substr($image->image_url, 0, 25).'..' }}
                                                </a></td>
                                            <td>
                                                <div class=" row justify-content-center ">
                                                    <a href="javascript:void(0)" onclick="loadImageEditModal({{$image->id}})" data-toggle="modal" data-id="{{$image->id}}" data-target="#edit-image">
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
                            <h4 class="card-title">{{ $formTitle }}</h4>
                            <form class="form pt-3" method="post" action="{{ $formURL }}" enctype="multipart/form-data">
                                @csrf
                                @isset($model)
                                <input type=hidden name=id value="{{(isset($model)) ? $model->id : ''}}">
                                @endisset

                                <div class="form-group">
                                    <label>Brand*</label>
                                    <div class="input-group mb-3">
                                        <select name=brand class="select2 form-control custom-select" style="width: 100%; height:36px;" required>
                                            <option value="" disabled selected>Pick From Active Brands</option>
                                            @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" @if(isset($model) && $brand->id == $model->MODL_BRND_ID)
                                                selected
                                                @elseif($brand->id == old('brand'))
                                                selected
                                                @endif
                                                >{{$brand->BRND_NAME}} ({{$brand->BRND_ACTV ? 'Active' : 'In-Active'}})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-danger">{{$errors->first('brand')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Type*</label>
                                    <div class="input-group mb-3">
                                        <select name=type class="select2 form-control custom-select" style="width: 100%; height:36px;" required>
                                            <option value="" disabled selected>Pick From Car Types</option>
                                            @foreach($types as $type)
                                            <option value="{{ $type->id }}" @if(isset($model) && $type->id == $model->MODL_TYPE_ID)
                                                selected
                                                @elseif($type->id == old('type'))
                                                selected
                                                @endif
                                                >{{$type->TYPE_NAME}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-danger">{{$errors->first('group')}}</small>
                                </div>

                                <div class="form-group">
                                    <label>Model Name*</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="far fa-copyright"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Model Name" name=name value="{{ (isset($model)) ? $model->MODL_NAME : old('name')}}" required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('name')}}</small>
                                </div>
                                <div class="form-group">
                                    <label>Year*</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon11"><i class="far fa-calendar"></i></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Model Year" name=year value="{{ (isset($model)) ? $model->MODL_YEAR : old('year')}}" required>
                                    </div>
                                    <small class="text-danger">{{$errors->first('year')}}</small>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Arabic Name</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-font"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name=arbcName placeholder="Arabic Name" value="{{ (isset($model)) ? $model->MODL_ARBC_NAME : old('arbcName')}}">
                                    </div>
                                    <small class="text-danger">{{$errors->first('arbcName')}}</small>
                                </div>


                                <div class="form-group bt-switch">
                                    <div class="col-md-5 m-b-15">
                                        <h4 class="card-title">Active</h4>
                                        <input type="checkbox" data-size="large" {{(isset($model) && $model->MODL_ACTV) ? 'checked' : ''}} data-on-color="success" data-off-color="danger"
                                        data-on-text="Active" data-off-text="Hidden" name="isActive">
                                    </div>
                                    <small class="text-muted">This model and all its linked cars can be hidden/published using this option</small>
                                </div>



                                <div class="form-group">
                                    <label for="input-file-now-custom-1">Model Image</label>
                                    <div class="input-group mb-3">
                                        <input type="file" id="input-file-now-custom-1" name=image class="dropify"
                                            data-default-file="{{ (isset($model->MODL_IMGE)) ? $model->getImageUrlAttribute() : old('image') }}" />
                                    </div>
                                    <small class="text-muted">Image size should be 346 * 224 -- It appears on the home page if this is a main model -- The background should be transparent or white in
                                        color</small><br>
                                    <small class="text-danger">{{$errors->first('image')}}</small>

                                </div>

                                <div class="form-group">
                                    <label for="input-file-now-custom-1">PDF Brochure</label>
                                    <div class="input-group mb-3">
                                        <input type="file" id="input-file-now-custom-1" name=pdf class="dropify"
                                            data-default-file="{{ (isset($model->MODL_BRCH)) ? $model->getPdfUrlAttribute() : old('pdf') }}" />
                                    </div>
                                    <small class="text-muted">PDF Brochure shall be used in case no interactive Brochure provided</small><br>
                                    <small class="text-danger">{{$errors->first('pdf')}}</small>

                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Model Overview</label>
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" name=overview rows="5">{{(isset($model)) ? $model->MODL_OVRV : old('overview')}}</textarea>
                                    </div>
                                    <small class="text-muted">Model Overview paragraph, shown on the home page and on the model page</small><br>
                                    <small class="text-danger">{{$errors->first('overview')}}</small>
                                </div>


                                <button type="submit" class="btn btn-success mr-2">Submit</button>
                                @if($isCancel)
                                <a href="{{url($homeURL) }}" class="btn btn-dark">Cancel</a>
                                @endif
                            </form>
                            <hr>
                            <h4 class="card-title">Delete Model</h4>
                            <button type="button" onclick="confirmAndGoTo('{{url('admin/models/delete/'.$model->id )}}', 'delete this Model and all the car linked to it ??')"
                                class="btn btn-danger mr-2">Delete <strong>All</strong> Model Data (Cars/Images linked to the model)</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>


<div id="edit-color" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Color</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form class="form pt-3" method="post" action="{{ $updateColorInfoURL }}" enctype="multipart/form-data">
                    @csrf
                    <input type=hidden name=modelID value="{{(isset($model)) ? $model->id : ''}}">
                    <input type=hidden name=id id=colorIDModal>

                    <div class="form-group">
                        <label>Color Name*</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Example: Baby Blue" name=name value="{{old('name')}}" id=nameModal>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Arabic Color Name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Example: ازرق زهري" name=arbcName value="{{old('arbcName')}}" id=arbcNameModal>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hex Value</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Example: FF650C" name=hex value="{{old('hex')}}" id=hexModal>
                        </div>
                    </div>
                    <div class=row>
                        <div class="col-4 form-group">
                            <label>Red Value</label>
                            <div class="input-group mb-3">
                                <input type="number" max=255 min=0 step="1" class="form-control" name=red id=redModal value="{{old('red')}}" required>
                            </div>
                        </div>

                        <div class="col-4 form-group">
                            <label>Green Value</label>
                            <div class="input-group mb-3">
                                <input type="number" max=256 min=0 step="1" class="form-control" name=green value="{{old('green')}}" id=greenModal required>
                            </div>
                        </div>


                        <div class="col-4 form-group">
                            <label>Blue Value</label>
                            <div class="input-group mb-3">
                                <input type="number" max=255 min=0 step="1" class="form-control" name=blue value="{{old('blue')}}" id=blueModal required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Alpha Value</label>
                        <div class="input-group mb-3">
                            <input type="number" max=100 min=0 step="1" class="form-control" name=alpha id=alphaModal value="{{old('alpha')}}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="input-file-now-custom-1">New Photo</label>
                        <div class="input-group mb-3">
                            <input type="file" id="input-file-now-custom-1" name=photo class="dropify" id=colorPhotoModal data-max-file-size="2M" />
                        </div>
                        <small class="text-muted">Optimum Resolution is 148 * 100</small>
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


<div id="edit-color" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Image</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form class="form pt-3" method="post" action="{{ $updateImageInfoURL }}" enctype="multipart/form-data">
                    @csrf
                    <input type=hidden name=modelID value="{{(isset($model)) ? $model->id : ''}}">
                    <input type=hidden name=id id=imageID value="">

                    <div class="form-group">
                        <label>Sort Value</label>
                        <div class="input-group mb-3">
                            <input type="number" max=255 min=0 step="1" class="form-control" name=sort id=sortModal required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="input-file-now-custom-1">New Photo</label>
                        <div class="input-group mb-3">
                            <input type="file" id="input-file-now-custom-1" name=photo class="dropify" id=photoModal data-max-file-size="2M" />
                        </div>
                        <small class="text-muted">Optimum Resolution is 300 * 150</small>
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

@endsection

@section('js_content')
<script>
    function loadColorEditModal (id) {
        
        var name = $('#colorName'+id).html();
        var arbcName = $('#colorArbcName'+id).html();
        var hex = $('#colorHex'+id).html();
        var red = $('#colorRed'+id).html();
        var blue = $('#colorBlue'+id).html();
        var green = $('#colorGreen'+id).html();
        var alpha = $('#colorAlpha'+id).html();
        var imgURL = $('#colorURL'+id).html();

        $(".modal-body #colorIDModal").val(id);
        $(".modal-body #nameModal").val(name);
        $(".modal-body #arbcNameModal").val(arbcName);
        $(".modal-body #hexModal").val(hex);
        $(".modal-body #redModal").val(red);
        $(".modal-body #blueModal").val(blue);
        $(".modal-body #greenModal").val(green);
        $(".modal-body #alphaModal").val(alpha);
        $(".modal-body #colorPhotoModal").attr("data-default-file", imgURL);
        $(".modal-body #colorPhotoModal").dropify();

    }

    function loadImageEditModal (id) {
        
        var name = $('#imageSort'+id).html();
        var imgURL = $('#imageURL'+id).html();

        $(".modal-body #imageIDModal").val(id);
        $(".modal-body #sortModal").val(name);
        $(".modal-body #photoModal").attr("data-default-file", imgURL);
        $(".modal-body #photoModal").dropify();

    }


    function deleteImage(id){
            Swal.fire({
                title: "Delete",
                text: "Are you sure you want to delete the image?",
                icon: "warning",
                showCancelButton: true,
            }).then((isConfirm) => {
                if(isConfirm.value){

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
            }});
    }

</script>
@endsection