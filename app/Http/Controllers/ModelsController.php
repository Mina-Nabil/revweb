<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarImage;
use App\Models\CarModel;
use App\Models\CarType;
use App\Models\ModelColor;
use App\Models\ModelImage;
use App\Services\FilesHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ModelsController extends Controller
{
    protected $data;
    protected $homeURL = 'admin/models/show';
    protected $profileURL = 'admin/models/profile/';

    public function profile($id)
    {
        $this->initProfileArr($id);
        $this->initAddArr($id);
        $this->data['formTitle'] = "Edit Model(" . $this->data['model']->MODL_NAME . ")";
        $this->data['formURL'] = url("admin/models/update");
        $this->data['imageFormURL'] = url("admin/models/add/image");
        $this->data['updateImageInfoURL'] = url("admin/models/update/image");
        $this->data['delImageUrl'] = url("admin/models/image/delete/");
        $this->data['isCancel'] = false;
        return view('models.profile', $this->data);
    }


    public function home()
    {
        $this->initDataArr();
        return view('models.show', $this->data);
    }
    public function add()
    {
        $this->initAddArr();
        $this->data['formTitle'] = "Add Car Model";
        $this->data['formURL'] = "admin/models/insert";
        $this->data['isCancel'] = false;
        return view('models.add', $this->data);
    }

    public function insert(Request $request)
    {

        $request->validate([
            "name"      => "required",
            "brand"      => "required|exists:brands,id",
            "type"      => "required|exists:types,id",
            "year"      => "required",
            "overview"  => "required_if:isActive,on",
            "image"  => "required_if:isActive,on|file",
            "pdf"  => "required_if:isActive,on|mimes:pdf",
        ]);

        $filesHandler = new FilesHandler();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $filesHandler->uploadFile($request->image, 'images/models/' . $request->name);
        }

        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $filesHandler->uploadFile($request->pdf, 'images/models/' . $request->name);
        }

        $isActive = $request->isActive == 'on' ? 1 : 0;

        $newCar = CarModel::create($request->brand, $request->type, $request->name, $request->arbcName, $request->year, $request->overview, $imagePath, $pdfPath, $isActive);
        if (!$newCar) {
            $filesHandler->deleteFile($pdfPath);
            $filesHandler->deleteFile($imagePath);
        }
        return redirect($this->profileURL . $newCar->id);
    }

    public function update(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);
        $model = CarModel::findOrFail($request->id);
        $filesHandler = new FilesHandler();

        $request->validate([
            "name" => "required",
            "arbcName" => "required_if:isActive,on",
            "brand"      => "required|exists:brands,id",
            "type"      => "required|exists:types,id",
            "year"      => "required",
            "overview"  => "required_if:isActive,on",
        ]);
        if (is_null($model->MODL_IMGE) || $model->MODL_IMGE == "")
            $request->validate([
                "image"  => "required_if:isActive,on|image",
            ]);

        if (is_null($model->MODL_PDF) || $model->MODL_PDF == "")
            $request->validate([
                "pdf"  => "required_if:isActive,on|mimes:pdf",
            ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $filesHandler->uploadFile($request->image, 'images/models/' . $request->name);
        }

        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $filesHandler->uploadFile($request->pdf, 'images/models/' . $request->name);
        }
        $isActive = $request->isActive == 'on' ? 1 : 0;
        if (!$model->updateInfo($request->brand, $request->type, $request->name, $request->arbcName, $request->year, $request->overview, $imagePath, $pdfPath, $isActive)) {
            $filesHandler->deleteFile($pdfPath);
            $filesHandler->deleteFile($imagePath);
        };

        return redirect($this->profileURL . $model->id);
    }

    public function toggleMain($id)
    {
        $model = CarModel::findOrFail($id);
        $model->toggleMain();
        return back();
    }

    public function toggleActive($id)
    {
        $model = CarModel::findOrFail($id);
        $model->toggleActive();
        return back();
    }
    ///////////images functions
    public function attachImage(Request $request)
    {
        $request->validate([
            "modelID"   =>  "required|exists:models,id",
            'name'      =>  'required',
            "photo"     =>  "nullable|file",
            'red'       =>  "nullable|max:256",
            'green'     =>  "nullable|max:256",
            'blue'      =>  "nullable|max:256",
            'alpha'     =>  "nullable|max:256",
            'hex'       =>  ['nullable', 'regex:/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);
        $model = CarModel::findOrFail($request->modelID);
        $filesHandler = new FilesHandler();
        $imageURL = NULL;
        if ($request->hasFile('photo')) {
            $imageURL = $filesHandler->uploadFile($request->photo, "cars/" . $model->MODL_NAME . '/colors//');
        }

        $model->colors()->create([
            "COLR_NAME" => $request->name,
            "COLR_ARBC_NAME" => $request->arbcName,
            "COLR_IMGE" => $imageURL ?? NULL,
            "COLR_HEX" => $request->hex,
            "COLR_RED" => $request->red,
            "COLR_GREN" => $request->green,
            "COLR_BLUE" => $request->blue,
            "COLR_ALPH" => $request->alpha
        ]);

        return back();
    }


    public function delImage($id)
    {
        $image = ModelImage::findOrFail($id);
        echo $image->deleteImage();
    }

    public function editImage(Request $request)
    {
        $request->validate([
            "id"        => "required",
            "modelID"   =>  "required|exists:models,id",
            'name'      =>  'required',
            "photo"     =>  "nullable|file",
            'red'       =>  "nullable|max:256",
            'green'     =>  "nullable|max:256",
            'blue'      =>  "nullable|max:256",
            'alpha'     =>  "nullable|max:256",
            'hex'       =>  ['nullable', 'regex:/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);
        $image = ModelColor::findOrFail($request->id);
        $image->editInfo(
            $request->name,
            $request->arbcName,
            $imageURL ?? NULL,
            $request->hex,
            $request->red,
            $request->green,
            $request->blue,
            $request->alpha
        );

        return back();
    }


    //////////////////// Data functions
    private function initProfileArr($modelID)
    {
        $this->data['model'] = CarModel::with('cars', 'type', 'brand', 'colors')->findOrFail($modelID);
        //Model Categories
        $this->data['items'] = $this->data['model']->cars;
        $this->data['title'] = "Available Categories";
        $this->data['subTitle'] = "Check all Available Model categories";
        $this->data['cols'] = ['Sort Value', 'Category', 'Price', 'Discount'];
        $this->data['atts'] = [
            'CAR_VLUE',
            ['dynamicUrl' => ['att' => 'CAR_CATG', 'val' => 'id', 'baseUrl' => 'admin/cars/profile/']],
            ['number' => ['att' => 'CAR_PRCE', 'decimals' => 0]],
            ['number' => ['att' => 'CAR_DISC', 'decimals' => 0]]
        ];
    }

    private function initDataArr()
    {
        $this->data['items'] = CarModel::with("brand")->orderBy('MODL_ACTV')->get();
        $this->data['title'] = "Available Models";
        $this->data['subTitle'] = "Check all Available Models";
        $this->data['cols'] = ['Image', 'Brand', 'Name', 'Arabic', 'Year', 'Active', 'Main', 'Overview'];
        $this->data['atts'] = [
            ['assetImg' => ['att' => 'MODL_IMGE']],
            ['foreign' => ['att' => 'BRND_NAME', 'rel' => 'brand']],
            ['dynamicUrl' => ['att' => 'MODL_NAME', 'val' => 'id', 'baseUrl' => 'admin/models/profile/']],
            ['dynamicUrl' => ['att' => 'MODL_ARBC_NAME', 'val' => 'id', 'baseUrl' => 'admin/models/profile/']],
            'MODL_YEAR',
            [
                'toggle' => [
                    "att"   =>  "MODL_ACTV",
                    "url"   =>  "admin/models/toggle/active/",
                    "states" => [
                        "1" => "Active",
                        "0" => "Hidden",
                    ],
                    "actions" => [
                        "1" => "hide the model",
                        "0" => "show the model",
                    ],
                    "classes" => [
                        "1" => "label-success",
                        "0" => "label-danger",
                    ],
                ]
            ],
            [
                'toggle' => [
                    "att"   =>  "MODL_MAIN",
                    "url"   =>  "admin/models/toggle/main/",
                    "states" => [
                        "1" => "True",
                        "0" => "False",
                    ],
                    "actions" => [
                        "1" => "hide the model from home page",
                        "0" => "show the model on the home page, please make sure the model has an image and an overview",
                    ],
                    "classes" => [
                        "1" => "label-info",
                        "0" => "label-warning",
                    ],
                ]
            ],
            ['comment' => ['att' => 'MODL_OVRV', 'title' => 'Overview']]
        ];
        $this->data['homeURL'] = $this->homeURL;
    }

    private function initAddArr()
    {
        $this->data['brands'] = Brand::all();
        $this->data['types'] = CarType::all();

        return view('models.add', $this->data);
    }
}
