@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 col-12">
        <x-datatable :id="$id??'myTable'" :title="$title" :subtitle="$subTitle" :cols="$cols" :items="$items" :atts="$atts" />
    </div>

    <div class="col-lg-4 col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $formTitle }}</h4>
                <form class="form pt-3" method="post" action="{{ url($formURL) }}" enctype="multipart/form-data">
                    @csrf
                    <input type=hidden name=id value="{{(isset($brand)) ? $brand->id : ''}}">
                    <div class="form-group">
                        <label>Brand Name*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon11"><i class="far fa-copyright"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Brand Name" name=name value="{{ (isset($brand)) ? $brand->BRND_NAME : old('name')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('name')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Arabic Name</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon22"><i class="fas fa-font"></i></span>
                            </div>
                            <input type="text" class="form-control" name=arbcName placeholder="Arabic Name" value="{{ (isset($brand)) ? $brand->BRND_ARBC_NAME : old('arbcName')}}">
                        </div>
                        <small class="text-danger">{{$errors->first('arbcName')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="input-file-now-custom-1">Brand Logo</label>
                        <div class="input-group mb-3">
                            <input type="file" id="input-file-now-custom-1" name=logo class="dropify"
                                data-default-file="{{ (isset($brand->BRND_LOGO)) ? asset( 'storage/'. $brand->BRND_LOGO ) : old('logo') }}" />
                        </div>
                        <small class="text-muted">Logo size should be 100 * 60 -- It appears on the bottom of every page in the popular Brands Section</small><br>
                        <small class="text-danger">{{$errors->first('logo')}}</small>
                    </div>


                    <div class="form-group bt-switch">
                        <div class="col-md-5 m-b-15">
                            <h4 class="card-title">Active</h4>
                            <input type="checkbox" data-size="large" {{(isset($brand) && $brand->BRND_ACTV) ? 'checked' : ''}} data-on-color="success" data-off-color="danger" data-on-text="Active" data-off-text="Hidden" name="isActive">
                        </div>
                        <small class="text-muted">All Brands models and cars can be hidden/published using this option</small>
                    </div>


                    <button type="submit" class="btn btn-success mr-2">Submit</button>
                    @if($isCancel)
                    <a href="{{url($homeURL) }}" class="btn btn-dark">Cancel</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection