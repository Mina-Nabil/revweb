@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-7 col-12">
        <x-datatable :id="$id??'myTable'" :title="$title" :subtitle="$subTitle" :cols="$cols" :items="$items" :atts="$atts" />
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">{{ $formTitle }}</h4>
                <form class="form pt-3" method="post" action="{{ url($formURL) }}" enctype="multipart/form-data">
                    @csrf
                    <input type=hidden name=id value="{{(isset($accessory)) ? $accessory->id : ''}}">

                    <div class="form-group">
                        <label>Name*</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon11"><i class="fab fa-youtube"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Accessory Name" name=name value="{{ (isset($accessory)) ? $accessory->ACSR_NAME : old('name')}}" required>
                        </div>
                        <small class="text-danger">{{$errors->first('name')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Arabic Name</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon22"><i class="fas fa-font"></i></span>
                            </div>
                            <input type="text" class="form-control" name=arbcName placeholder="Arabic Name" value="{{ (isset($accessory)) ? $accessory->ACSR_ARBC_NAME : old('arbcName')}}">
                        </div>
                        <small class="text-danger">{{$errors->first('arbcName')}}</small>
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