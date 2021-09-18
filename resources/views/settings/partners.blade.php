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
                    <input type=hidden name=id value="{{(isset($partner)) ? $partner->id : ''}}">
                    <div class="form-group">
                        <label>Partner Name*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon11"><i class="far fa-copyright"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Partner Name" name=name value="{{ (isset($partner)) ? $partner->PRTR_NAME : old('name')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('name')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="input-file-now-custom-1">Partner Logo</label>
                        <div class="input-group mb-3">
                            <input type="file" id="input-file-now-custom-1" name=image class="dropify"
                                data-default-file="{{ (isset($partner->PRTR_IMGE)) ? asset( 'storage/'. $partner->PRTR_IMGE ) : old('image') }}" />
                        </div>
                        <small class="text-muted">Logo size should be 100 * 60 -- It appears on the bottom of every page in the popular Partners Section</small><br>
                        <small class="text-danger">{{$errors->first('image')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Website*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon22"><i class="fas fa-globe"></i></span>
                            </div>
                            <input type="text" class="form-control" name=website placeholder="Partner Website" value="{{ (isset($partner)) ? $partner->PRTR_URL : old('website')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('website')}}</small>
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