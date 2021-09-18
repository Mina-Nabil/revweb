<?php

namespace App\Http\Controllers;

use App\Models\Accessories;
use App\Models\Car;
use App\Models\CarAccessory;
use App\Models\CarImage;
use App\Models\CarModel;
use Illuminate\Http\Request;

class CarsController extends Controller
{

    protected $data;
    protected $homeURL = "admin/cars/show";

    public function home()
    {
        $this->initDataArr();
        return view('cars.show', $this->data);
    }

    public function profile($id)
    {
        $this->initProfileArr($id);
        $this->data['formTitle'] = "Edit " . $this->data['car']->model->MODL_NAME . ' ' . $this->data['car']->CAR_CATG;
        $this->data['formURL'] = url("admin/cars/update");
        $this->data['updateImageInfoURL'] = url("admin/cars/update/image");
        $this->data['toggleOffer'] = url("admin/cars/toggle/offer");
        $this->data['toggleTrending'] = url("admin/cars/toggle/trending");
        $this->data['isCancel'] = false;
        return view('cars.profile', $this->data);
    }

    public function insert(Request $request)
    {
        $request->validate([
            "model"     => "required|exists:models,id",
            "category"  => "required",
            "price"     => "required",
            "cc"        =>  "required_if:isActive,on",
            "hpwr"      =>  "required_if:isActive,on",
            "torq"      =>  "required_if:isActive,on",
            "trns"      =>  "required_if:isActive,on",
            "speed"     =>  "required_if:isActive,on",
            "height"    =>  "required_if:isActive,on",
            "rims"      =>  "required_if:isActive,on",
            "tank"      =>  "required_if:isActive,on",
            "seat"      =>  "required_if:isActive,on",
            "dimn"      =>  "required_if:isActive,on",
        ]);

        $car = new Car();
        //info
        $car->CAR_MODL_ID   =   $request->model;
        $car->CAR_CATG      =   $request->category;
        $car->CAR_PRCE      =   $request->price;
        $car->CAR_DISC      =   $request->discount ?? 0;
        $car->CAR_VLUE      =   $request->sort ?? 500;

        //specs
        $car->CAR_ENCC      =   $request->cc;
        $car->CAR_HPWR      =   $request->hpwr;
        $car->CAR_TORQ      =   $request->torq;
        $car->CAR_TRNS      =   $request->trns;
        $car->CAR_ACC       =   $request->acc;
        $car->CAR_TPSP      =   $request->speed;
        $car->CAR_HEIT      =   $request->height;
        $car->CAR_TRNK      =   $request->tank;
        $car->CAR_RIMS      =   $request->rims;
        $car->CAR_SEAT      =   $request->seat;
        $car->CAR_DIMN      =   $request->dimn;

        //overview
        $car->CAR_TTL1      =   $request->title1;
        $car->CAR_PRG1      =   $request->prgp1;
        $car->CAR_TTL2      =   $request->title2;
        $car->CAR_PRG2      =   $request->prgp2;

        $car->save();

        return redirect('admin/cars/profile/' . $car->id);
    }

    public function update(Request $request)
    {
        $request->validate([
            "id"        => "required",
            "model"     => "required|exists:models,id",
            "category"  => "required",
            "price"     => "required",
            "cc"        =>  "required_if:isActive,on",
            "hpwr"      =>  "required_if:isActive,on",
            "torq"      =>  "required_if:isActive,on",
            "trns"      =>  "required_if:isActive,on",
            "speed"     =>  "required_if:isActive,on",
            "height"    =>  "required_if:isActive,on",
            "rims"      =>  "required_if:isActive,on",
            "tank"      =>  "required_if:isActive,on",
            "seat"      =>  "required_if:isActive,on",
            "dimn"      =>  "required_if:isActive,on",
        ]);

        $car = Car::findOrFail($request->id);

        //info
        $car->CAR_MODL_ID   =   $request->model;
        $car->CAR_CATG      =   $request->category;
        $car->CAR_PRCE      =   $request->price;
        $car->CAR_DISC      =   $request->discount ?? 0;
        $car->CAR_VLUE      =   $request->sort ?? 500;

        //specs
        $car->CAR_ENCC      =   $request->cc;
        $car->CAR_HPWR      =   $request->hpwr;
        $car->CAR_TORQ      =   $request->torq;
        $car->CAR_TRNS      =   $request->trns;
        $car->CAR_ACC       =   $request->acc;
        $car->CAR_TPSP      =   $request->speed;
        $car->CAR_HEIT      =   $request->height;
        $car->CAR_TRNK      =   $request->tank;
        $car->CAR_RIMS      =   $request->rims;
        $car->CAR_SEAT      =   $request->seat;
        $car->CAR_DIMN      =   $request->dimn;

        //overview
        $car->CAR_TTL1      =   $request->title1;
        $car->CAR_PRG1      =   $request->prgp1;
        $car->CAR_TTL2      =   $request->title2;
        $car->CAR_PRG2      =   $request->prgp2;

        $car->save();

        return redirect('admin/cars/profile/' . $car->id);
    }


    public function add()
    {
        $this->initAddArr();
        $this->data['formTitle'] = "New Car Profile";
        $this->data['formURL'] = "admin/cars/insert";
        $this->data['isCancel'] = false;
        return view('cars.add', $this->data);
    }


    ///////////images functions
    public function attachImage(Request $request)
    {
        $request->validate([
            "carID" => "required|exists:cars,id",
            "photo" => "file",
            'value' => 'required'
        ]);
        $car = Car::findOrFail($request->carID);
        $newImage = new CarImage();
        if ($request->hasFile('photo')) {
            $newImage->CIMG_URL = $request->photo->store('images/cars/' . $car->CAR_CATG, 'public');
        }
        $newImage->CIMG_CAR_ID = $request->carID;
        $newImage->CIMG_VLUE = $request->value;
        $newImage->save();
        $newImage->compress();
        return back();
    }


    public function deleteImage($id)
    {
        $image = CarImage::findOrFail($id);
        echo $image->deleteImage();
    }

    public function editImage(Request $request){
        $request->validate([
            "id"    => "required",
            'value' => 'required',
        ]);
        $image = CarImage::findOrFail($request->id);

        $image->CIMG_VLUE = $request->value;
        echo $image->save();

       }

    public function linkAccessory(Request $request)
    {
        $request->validate([
            'carID' => 'required|exists:cars,id',
            'accessID' => 'required|exists:accessories,id'
        ]);

        $car = Car::findOrFail($request->carID);
        $accessory = Accessories::findOrFail($request->accessID);
        $res = $car->accessories()->syncWithoutDetaching([$accessory->id => ['ACCR_VLUE' => ($request->value ?? '')]]);
        if (count($res["attached"]) > 0 || count($res["updated"]) > 0)
            echo 1;
        else
            echo 0;
    }

    public function deleteAccessoryLink($carID, $accessoryId)
    {
        $car = Car::findOrFail($carID);
        $accessory = Accessories::findOrFail($accessoryId);
        echo $car->accessories()->detach($accessory);
    }

    public function loadData(Request $request){
        $car = Car::with(["model", "model.brand"])->findOrFail($request->carID);
        echo json_encode($car);
        return;
    }

    public function loadAccessories(Request $request){

        $request->validate([
            "id"        =>  "required",
            "carID"     =>  "required"
        ]);

        $otherCar = Car::findOrFail($request->carID);
        $car = Car::findOrFail($request->id);

        $otherAccessories = $otherCar->getAccessories();
 
        $otherAccessories = $otherAccessories->mapWithKeys(function ($item){
            return [$item->ACCR_ACSR_ID =>[ "ACCR_VLUE" => $item->ACCR_VLUE ]];
        });

        $car->accessories()->sync($otherAccessories->all());

       return back();
    }

    public function toggleTrending(Request $request){

        $request->validate([
            "carID"     =>  "required"
        ]);

        $car = Car::findOrFail($request->carID);
        echo $car->toggleTrending();

    }

    public function toggleOffer(Request $request){

        $request->validate([
            "carID"     =>  "required"
        ]);

        $car = Car::findOrFail($request->carID);
        echo $car->toggleOffer();
    }


    //////////////////// Data functions
    private function initProfileArr($carID)
    {
        $this->data['car'] = Car::with('model', 'model.brand', 'model.type', 'accessories', 'images')->findOrFail($carID);
        $this->data['cars'] = Car::with('model', 'model.brand')->get();

        $this->data['accessories'] = $this->data['car']->getFullAccessoriesArray();
        $this->data['unlinkAccessoryURL'] = url('admin/cars/unlink/accessory/');
        $this->data['linkAccessoryURL'] = url('admin/cars/link/accessory');
        $this->data['loadAccessoriesURL'] = url('admin/cars/load/accessories');
        $this->data['loadCarURL'] = url("admin/cars/load/data");

        //Images table
        $this->data['images'] = $this->data['car']->images;

        //edit form
        $this->data['models'] = CarModel::with('brand', 'type')->get();

        //add photo form
        $this->data['imageFormURL'] = url('admin/cars/images/add');
        $this->data['delImageUrl']  = url('admin/cars/images/del/');
    }

    private function initDataArr()
    {
        $this->data['items'] = Car::with(["model.brand", "model.type"])->orderBy('CAR_VLUE', 'desc')->get();
        $this->data['title'] = "Available Cars";
        $this->data['subTitle'] = "Check all Available Cars";
        $this->data['cols'] = ['Category', 'Model', 'Year', 'Active?'];
        $this->data['atts'] = [
            ['dynamicUrl' => ['att' => 'CAR_CATG', 'val' => 'id', 'baseUrl' => 'admin/cars/profile/']],
            ['foreignUrl' => ['rel' => 'model', 'att' => 'MODL_NAME', 'baseUrl' => 'admin/models/profile', 'urlAtt' => 'id']],
            ['foreign' => ['rel' => 'model', 'att' => 'MODL_YEAR']],
            [
                'state' => [
                    "att"   =>  "CAR_ACTV",
                    "text" => [
                        "1" => "Active",
                        "0" => "Hidden",
                    ],
                    "classes" => [
                        "1" => "label-success",
                        "0" => "label-danger",
                    ],
                ]
            ],
        ];
        $this->data['homeURL'] = $this->homeURL;
    }

    private function initAddArr()
    {
        $this->data['models'] = CarModel::with('brand', 'type')->get();
        $this->data['cars'] = Car::with('model', 'model.brand')->get();
        $this->data['loadCarURL'] = url("admin/cars/load/data");
    }
}
