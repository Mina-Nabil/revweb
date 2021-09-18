@extends('layouts.app')

@section('content')
<script>
    function IsValidJSONString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
            return true;
    }
  
    function importCarProfile(){

        var http = new XMLHttpRequest();
        var url = '{{$loadCarURL}}';
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
            console.log(json)
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
</script>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h3 class="card-title">{{ $formTitle }}</h3>
                <form class="form pt-3" method="post" action="{{ url($formURL) }}" enctype="multipart/form-data">
                    @csrf
                    @isset($car)
                    <input type=hidden name=id value="{{(isset($car)) ? $car->id : ''}}">
                    @endisset

                    <h4 class="card-title">Car Profile Status</h4>

                    <div class="form-group bt-switch">
                        <div class="col-md-5 m-b-15">
                            <h4 class="card-title">Active</h4>
                            <input type="checkbox" data-size="large" {{(isset($car) && $car->CAR_ACTV) ? 'checked' : ''}} data-on-color="success" data-off-color="danger" data-on-text="Active"
                                data-off-text="Hidden" name="isActive">
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
                            <input type="text" class="form-control" placeholder="Category Name - Example: Allure+" name=category value="{{ (isset($car)) ? $car->CAR_CATG : old('category')}}" required>
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
                    <hr>
                    <div class=row>
                        <div class=col-9>
                            <h4 class="card-title">Car Specifications</h4>
                        </div>
                        <div class=col-3>
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
                            <input type="text" class="form-control" name=cc id=cc placeholder="Enter Engine CC Specs, Example: 1500 Turbo" value="{{ (isset($car)) ? $car->CAR_ENCC : old('cc')}}">
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
                            <input type="text" class="form-control" name=torq id=torq placeholder="Enter torque in Nm@rpm, Example: 230@1750" value="{{ (isset($car)) ? $car->CAR_TORQ : old('torq')}}">
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
                            <input type="number" class="form-control" name=speed id=speed placeholder="Car Top Speed in Km/h, Example: 220" value="{{ (isset($car)) ? $car->CAR_TPSP : old('speed')}}">
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
                            <input type="number" class="form-control" name=rims id=rims placeholder="Rims Tyre measure, Example: 16" value="{{ (isset($car)) ? $car->CAR_RIMS : old('rims')}}">
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

                    <hr>
                    <h4 class="card-title">Car Overview <small class="text-muted">Optional</small></h4>
              

                    <div class="form-group">
                        <label for="exampleInputEmail1">Title 1</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name=title1 placeholder="First Paragraph title" value="{{ (isset($car)) ? $car->CAR_TTL1 : old('title1')}}">
                        </div>
                        <small class="text-muted">First Car Title in overview section, shown on the car details page</small><br>
                        <small class="text-danger">{{$errors->first('title1')}}</small>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1">Paragraph 1</label>
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
            </div>
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
                        <?php foreach ($cars as $car) { ?>
                        <option value="{{$car->id}}">
                            {{$car->model->brand->BRND_NAME}}: {{$car->model->MODL_NAME}} {{$car->CAR_CATG}} {{$car->model->MODL_YEAR}}
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
@endsection