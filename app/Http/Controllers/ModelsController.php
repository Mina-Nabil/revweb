<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\CarType;
use App\Models\ModelImage;
use Illuminate\Http\Request;
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
            "overview"  => "required_if:isMain,on",
            "image"  => "required_if:isMain,on|file",
            "background"  => "required_if:isMain,on|image",
            "pdf"  => "required_if:isMain,on|mimes:pdf",
        ]);

        $model = new CarModel();
        $model->MODL_BRND_ID = $request->brand;
        $model->MODL_TYPE_ID = $request->type;
        $model->MODL_NAME = $request->name;
        $model->MODL_ARBC_NAME = $request->arbcName;
        $model->MODL_BRCH = $request->brochureCode;
        $model->MODL_YEAR = $request->year;
        $model->MODL_OVRV = $request->overview;
        if ($request->hasFile('image')) {
            $model->MODL_IMGE = $request->image->store('images/models/' . $model->MODL_NAME, 'public');
        }
        if ($request->hasFile('background')) {
            $model->MODL_BGIM = $request->background->store('images/models/' . $model->MODL_NAME, 'public');
        }
        if ($request->hasFile('pdf')) {
            $model->MODL_PDF = $request->pdf->store('images/models/' . $model->MODL_NAME, 'public');
        }
        $model->MODL_ACTV = $request->isActive == 'on' ? 1 : 0;
        $model->MODL_MAIN = $request->isMain == 'on' ? 1 : 0;


        $model->save();
        return redirect($this->profileURL . $model->id);
    }

    public function update(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);
        $model = CarModel::findOrFail($request->id);

        $request->validate([
            "name" => "required",
            "brand"      => "required|exists:brands,id",
            "type"      => "required|exists:types,id",
            "year"      => "required",
            "overview"  => "required_if:isMain,on",
        ]);
        if (is_null($model->MODL_IMGE) || $model->MODL_IMGE=="")
            $request->validate([
                "image"  => "required_if:isMain,on|image",
            ]);
        if (is_null($model->MODL_BGIM) || $model->MODL_BGIM=="")
            $request->validate([
                "background"  => "required_if:isMain,on|image",
            ]);
        if (is_null($model->MODL_PDF) || $model->MODL_PDF=="")
            $request->validate([
                "pdf"  => "required_if:isMain,on|mimes:pdf",
            ]);

        $model->MODL_BRND_ID = $request->brand;
        $model->MODL_TYPE_ID = $request->type;
        $model->MODL_NAME = $request->name;
        $model->MODL_ARBC_NAME = $request->arbcName;
        $model->MODL_BRCH = $request->brochureCode;
        $model->MODL_YEAR = $request->year;
        if ($request->hasFile('image')) {
            $model->MODL_IMGE = $request->image->store('images/models/' . $model->MODL_NAME, 'public');
        }
        if ($request->hasFile('background')) {
            $model->MODL_BGIM = $request->background->store('images/models/' . $model->MODL_NAME, 'public');
        }
        if ($request->hasFile('pdf')) {
            $model->MODL_PDF = $request->pdf->store('images/models/' . $model->MODL_NAME, 'public');
        }
        $model->MODL_ACTV = $request->isActive == 'on' ? 1 : 0;
        $model->MODL_MAIN = $request->isMain == 'on' ? 1 : 0;
        $model->MODL_OVRV = $request->overview;

        $model->save();

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
            "modelID" => "required|exists:models,id",
            "photo" => "file",
            'value' => 'required',
            'color' => 'required',
        ]);
        $model = CarModel::findOrFail($request->modelID);
        $newImage = new ModelImage();
        if ($request->hasFile('photo')) {
            $newImage->MOIM_URL = $request->photo->store('images/models/' . $model->MODL_NAME, 'public');
        }
        $newImage->MOIM_MODL_ID = $request->modelID;
        $newImage->MOIM_SORT = $request->value;
        $newImage->MOIM_COLR = $request->color;
        $newImage->save();
        $newImage->compress();
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
            "id"    => "required",
            'value' => 'required',
            'color' => 'required',
        ]);
        $image = ModelImage::findOrFail($request->id);

        $image->MOIM_SORT = $request->value;
        $image->MOIM_COLR = $request->color;
        echo $image->save();
    }


    //////////////////// Data functions
    private function initProfileArr($modelID)
    {
        $this->data['model'] = CarModel::with('cars', 'type', 'brand', 'colorImages')->findOrFail($modelID);
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
        $this->data['items'] = CarModel::orderBy('MODL_ACTV')->get();
        $this->data['title'] = "Available Models";
        $this->data['subTitle'] = "Check all Available Models";
        $this->data['cols'] = ['Image', 'Name', 'Arabic', 'Year', 'Active', 'Main', 'Overview'];
        $this->data['atts'] = [
            ['assetImg' => ['att' => 'MODL_IMGE']],
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
