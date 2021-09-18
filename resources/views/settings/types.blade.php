@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 col-12">
        <x-datatable :id="$id??'myTable'" :title="$title" :subtitle="$subTitle" :cols="$cols" :items="$items" :atts="$atts" />
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $formTitle }}</h4>
                <form class="form pt-3" method="post" action="{{ url($formURL) }}" enctype="multipart/form-data">
                    @csrf
                    <input type=hidden name=id value="{{(isset($type)) ? $type->id : ''}}">

                    <div class="form-group">
                        <label>Type*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon11"><i class="fas fa-car"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Type" name=type value="{{ (isset($type)) ? $type->TYPE_NAME : old('type')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('type')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Arabic Name</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon22"><i class="fas fa-font"></i></span>
                            </div>
                            <input type="text" class="form-control" name=arbcName placeholder="Arabic Name" value="{{ (isset($type)) ? $type->TYPE_ARBC_NAME : old('arbcName')}}">
                        </div>
                        <small class="text-danger">{{$errors->first('arbcName')}}</small>
                    </div>

                    <div class="form-group bt-switch">
                        <div class="col-md-5 m-b-15">
                            <h4 class="card-title">Main Type</h4>
                            <input type="checkbox" data-size="large" {{(isset($type) && $type->TYPE_MAIN) ? 'checked' : ''}} data-on-color="info" data-off-color="warning" data-on-text="Yes" data-off-text="No" name="isActive">
                        </div>
                        <small class="text-muted">Set it to 'Yes' to show the type in the home page main types</small>
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