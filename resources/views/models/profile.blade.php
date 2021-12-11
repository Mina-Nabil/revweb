@extends('layouts.app')


@section('content')

<div class="row">
    <!-- Column -->
    <div class="col-lg-4 col-xlg-3 col-md-5">
        <div class="card"> <img class="card-img" src="{{  (isset($model->MODL_IMGE)) ? asset( 'storage/'. $model->MODL_IMGE ) : asset('images/def-car.png')}}" alt="Card image">
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
                <li class="nav-item"> <a class="nav-link" data-toggle="tab" href="#colors" role="tab">Color Images</a> </li>
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
                                @isset($model->MODL_PDF)
                                <embed class="m-t-10" src="{{asset('storage/' . $model->MODL_PDF)}}" width="100%" height="375px">
                                @endisset
                            </div>
                        </div>
                        <hr>

                        <div class=row>
                            <div class="col-12 b-r">
                                <strong>Background Image</strong>
                                <img class="card-img m-t-10" src="{{  (isset($model->MODL_BGIM)) ? asset( 'storage/'. $model->MODL_BGIM ) : asset('images/def-car.png')}}" alt="Card image">
                            </div>
                        </div>
                        <hr>
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
                                    <img class="img-fluid" src="{{ asset( 'storage/'. $image->MOIM_URL ) }} "
                                        style="max-height:560px; max-width:900px; display: block;  margin-left: auto;  margin-right: auto;">
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
                        <h4 class="card-title">Add New Car Image</h4>
                        <form class="form pt-3" method="post" action="{{ $imageFormURL }}" enctype="multipart/form-data">
                            @csrf
                            <input type=hidden name=modelID value="{{(isset($model)) ? $model->id : ''}}">

                            <div class="form-group">
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

                            <div class="form-group">
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

                            <div class="form-group">
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

                            <div class="form-group">
                                <label>Red Value</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                    </div>
                                    <input type="number" max=255 min=0 step="1" class="form-control" name=red value="{{old('red')}}" required>
                                </div>
                                <small class="text-muted">Model Color Red Value under RGB code</small>
                                <small class="text-danger">{{$errors->first('red')}}</small>
                            </div>

                            <div class="form-group">
                                <label>Blue Value</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                    </div>
                                    <input type="number" max=255 min=0 step="1" class="form-control" name=blue value="{{old('blue')}}" required>
                                </div>
                                <small class="text-muted">Model Color Blue Value under RGB code</small>
                                <small class="text-danger">{{$errors->first('blue')}}</small>
                            </div>

                            <div class="form-group">
                                <label>Green Value</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon11"><i class="fas fa-palette"></i></span>
                                    </div>
                                    <input type="number" max=256 min=0 step="1" class="form-control" name=green value="{{old('green')}}" required>
                                </div>
                                <small class="text-muted">Model Color Green Value under RGB code</small>
                                <small class="text-danger">{{$errors->first('green')}}</small>
                            </div>

                            <div class="form-group">
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

                            <div class="form-group">
                                <label for="input-file-now-custom-1">New Photo</label>
                                <div class="input-group mb-3">
                                    <input type="file" id="input-file-now-custom-1" name=photo class="dropify" data-default-file="{{ old('photo') }}" data-max-file-size="2M" />
                                </div>
                                <small class="text-muted">Optimum Resolution is 346 * 224</small>
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
                                        @foreach ($model->colors as $image)
                                        <div style="display: none">
                                            <div id="imageName{{$image->id}}">{{$image->COLR_NAME}}</div>
                                            <div id="imageArbcName{{$image->id}}">{{$image->COLR_ARBC_NAME}}</div>
                                            <div id="imageHex{{$image->id}}">{{$image->COLR_HEX}}</div>
                                            <div id="imageRed{{$image->id}}">{{$image->COLR_RED}}</div>
                                            <div id="imageBlue{{$image->id}}">{{$image->COLR_BLUE}}</div>
                                            <div id="imageGreen{{$image->id}}">{{$image->COLR_GREN}}</div>
                                            <div id="imageAlpha{{$image->id}}">{{$image->COLR_ALPH}}</div>
                                            <div id="imageURL{{$image->id}}">{{$image->COLR_IMGE}}</div>
                                        </div>
                                        <tr>
                                            <td>rgb({{$image->COLR_RED}}, {{$image->COLR_GREN}}, {{$image->COLR_BLUE}})</td>
                                            <td>{{$image->COLR_NAME}}-{{$image->COLR_ARBC_NAME}}</td>
                                            <td>
                                                @isset($image->COLR_IMGE)
                                                <img src="{{ $image->image_url }} " width="60px">
                                                @endisset
                                            </td>
                                            <td><a target="_blank" href="{{ asset( 'storage/'. $image->COLR_IMGE ) }}">
                                                    {{(strlen($image->COLR_IMGE) < 25) ? $image->COLR_IMGE : substr($image->COLR_IMGE, 0, 25).'..' }}
                                                </a></td>
                                            <td>
                                                <div class=" row justify-content-center ">
                                                    <a href="javascript:void(0)" onclick="loadEditModal({{$image->id}})" data-toggle="modal" data-id="{{$image->id}}" data-target="#edit-image">
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

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Interactive Brochure</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon22"><i class="fas fa-barcode"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name=brochureCode placeholder="Interactive brochure code, example: 452336e5-81ed-43be-a4be-f7552f6366fd "
                                            value="{{ (isset($model)) ? $model->MODL_BRCH : old('brochureCode')}}">
                                    </div>
                                    <small class="text-muted">Use only the code written after the indesign url, https://indd.adobe.com/view/<strong>452336e5-81ed-43be-a4be-f7552f6366fd</strong>
                                    </small><br>
                                    <small class="text-danger">{{$errors->first('brochureCode')}}</small>
                                </div>


                                <div class="form-group bt-switch">
                                    <div class="col-md-5 m-b-15">
                                        <h4 class="card-title">Active</h4>
                                        <input type="checkbox" data-size="large" {{(isset($model) && $model->MODL_ACTV) ? 'checked' : ''}} data-on-color="success" data-off-color="danger"
                                        data-on-text="Active" data-off-text="Hidden" name="isActive">
                                    </div>
                                    <small class="text-muted">This model and all its linked cars can be hidden/published using this option</small>
                                </div>

                                <div class="form-group bt-switch">
                                    <div class="col-md-5 m-b-15">
                                        <h4 class="card-title">Main</h4>
                                        <input type="checkbox" data-size="large" {{(isset($model) && $model->MODL_MAIN) ? 'checked' : ''}} data-on-color="success" data-off-color="danger"
                                        data-on-text="Yes" data-off-text="No" name="isMain">
                                    </div>
                                    <small class="text-muted">The model can be published on the home page using this option</small>
                                </div>

                                <div class="form-group">
                                    <label for="input-file-now-custom-1">Model Image</label>
                                    <div class="input-group mb-3">
                                        <input type="file" id="input-file-now-custom-1" name=image class="dropify"
                                            data-default-file="{{ (isset($model->MODL_IMGE)) ? asset( 'storage/'. $model->MODL_IMGE ) : old('image') }}" />
                                    </div>
                                    <small class="text-muted">Image size should be 346 * 224 -- It appears on the home page if this is a main model -- The background should be transparent or white in
                                        color</small><br>
                                    <small class="text-danger">{{$errors->first('image')}}</small>

                                </div>

                                <div class="form-group">
                                    <label for="input-file-now-custom-1">Background Image</label>
                                    <div class="input-group mb-3">
                                        <input type="file" id="input-file-now-custom-1" name=background class="dropify"
                                            data-default-file="{{ (isset($model->MODL_BGIM)) ? asset( 'storage/'. $model->MODL_BGIM ) : old('background') }}" />
                                    </div>
                                    <small class="text-muted">Image size should be 1920 * 400 -- It appears on the models listing page</small><br>
                                    <small class="text-danger">{{$errors->first('background')}}</small>

                                </div>

                                <div class="form-group">
                                    <label for="input-file-now-custom-1">PDF Brochure</label>
                                    <div class="input-group mb-3">
                                        <input type="file" id="input-file-now-custom-1" name=pdf class="dropify"
                                            data-default-file="{{ (isset($model->MODL_PDF)) ? asset( 'storage/'. $model->MODL_PDF ) : old('pdf') }}" />
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


<div id="edit-image" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Color</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form class="form pt-3" method="post" action="{{ $updateImageInfoURL }}" enctype="multipart/form-data">
                    @csrf
                    <input type=hidden name=id id=modalImageID value="">

                    <div class="form-group">
                        <label>Color Name*</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Example: Baby Blue" name=name id=nameModal>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Arabic Color Name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Example: ازرق زهري" name=arbcName id=arbcNameModal>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Hex Value</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Example: FF650C" name=hex id=hexModal>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Red Value</label>
                        <div class="input-group mb-3">
                            <input type="number" max=255 min=0 step="1" class="form-control" name=red id=redModal required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Blue Value</label>
                        <div class="input-group mb-3">
                            <input type="number" max=255 min=0 step="1" class="form-control" name=blue id=blueModal required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Green Value</label>
                        <div class="input-group mb-3">
                            <input type="number" max=256 min=0 step="1" class="form-control" name=green value="{{old('green')}}" id=greenModal required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Alpha Value</label>
                        <div class="input-group mb-3">
                            <input type="number" max=100 min=0 step="1" class="form-control" name=alpha id=alphaModal required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="input-file-now-custom-1">New Photo</label>
                        <div class="input-group mb-3">
                            <input type="file" id="input-file-now-custom-1" name=photo class="dropify" id=photoModal data-max-file-size="2M" />
                        </div>
                        <small class="text-muted">Optimum Resolution is 346 * 224</small>
                    </div>

                    <div class="col-lg-3">
                        <div class="form-group col-12 m-t-10">
                            <button onclick="updateImageInfo()" class="btn btn-success waves-effect waves-light m-r-20">Submit</button>
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
    function loadEditModal (id) {
        
        console.log("HAHAHAHHA");

        var name = $('#imageName'+id).html();
        var arbcName = $('#imageArbcName'+id).html();
        var hex = $('#imageHex'+id).html();
        var red = $('#imageRed'+id).html();
        var blue = $('#imageBlue'+id).html();
        var green = $('#imageGreen'+id).html();
        var alpha = $('#imageAlpha'+id).html();
        var imgURL = $('#imageURL'+id).html();

        $(".modal-body #nameModal").val(name);
        $(".modal-body #arbcNameModal").val(arbcName);
        $(".modal-body #hexModal").val(hex);
        $(".modal-body #redModal").val(red);
        $(".modal-body #blueModal").val(blue);
        $(".modal-body #greenModal").val(green);
        $(".modal-body #alphaModal").val(alpha);
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