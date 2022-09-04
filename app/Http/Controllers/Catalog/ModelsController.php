<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Cars\AdjustmentOption;
use App\Models\Cars\Brand;
use App\Models\Cars\CarModel;
use App\Models\Cars\CarType;
use App\Models\Cars\ModelAdjustment;
use App\Models\Cars\ModelColor;
use App\Models\Cars\ModelImage;
use App\Services\FilesHandler;
use Illuminate\Http\Request;

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
        $this->data['colorFormURL'] = url("admin/models/add/color");
        $this->data['updateColorInfoURL'] = url("admin/models/update/color");
        $this->data['updateImageInfoURL'] = url("admin/models/update/image");
        $this->data['delImageUrl'] = url("admin/models/image/delete/");
        $this->data['addAdjustmentFormURL'] = url("admin/models/adjustment/add");
        $this->data['editAdjustmentFormURL'] = url("admin/models/adjustment/edit");
        $this->data['toggleAdjustmentFormURL'] = url("admin/models/adjustment/state/toggle");
        $this->data['addOptionFormURL'] = url("admin/models/options/add");
        $this->data['editOptionFormURL'] = url("admin/models/options/edit");
        $this->data['toggleOptionURL'] = url("admin/models/options/state/toggle");
        $this->data['defaultOptionURL'] = url("admin/models/options/set/default");
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
            $imagePath = $filesHandler->uploadFile($request->image, 'models/' . $request->name . '/images');
        }

        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $filesHandler->uploadFile($request->pdf, 'models/' . $request->name . '/pdfs');
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

        if (is_null($model->MODL_BRCH) || $model->MODL_BRCH == "")
            $request->validate([
                "pdf"  => "required_if:isActive,on|mimes:pdf",
            ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $filesHandler->uploadFile($request->image, 'models/' . $request->name . '/images');
        }

        $pdfPath = null;
        if ($request->hasFile('pdf')) {
            $pdfPath = $filesHandler->uploadFile($request->pdf, 'models/' . $request->name . '/pdfs');
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
    ///////////color images functions
    public function attachColor(Request $request)
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
            $imageURL = $filesHandler->uploadFile($request->photo, "models/" . $model->MODL_NAME . '/colors/' . $request->name);
        }

        if (!$model->colors()->create([
            "COLR_NAME" => $request->name,
            "COLR_ARBC_NAME" => $request->arbcName,
            "COLR_IMGE" => $imageURL ?? NULL,
            "COLR_HEX" => $request->hex,
            "COLR_RED" => $request->red,
            "COLR_GREN" => $request->green,
            "COLR_BLUE" => $request->blue,
            "COLR_ALPH" => $request->alpha
        ])) {
            $filesHandler->deleteFile($imageURL);
        }

        return back();
    }


    public function delColor($id)
    {
        $image = ModelColor::findOrFail($id);
        echo $image->deleteImage();
    }

    public function editColor(Request $request)
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
        $color = ModelColor::findOrFail($request->id);
        $color->load("model");
        $filesHandler = new FilesHandler();
        $oldURL = $color->COLR_IMGE;
        $imageURL = NULL;
        if ($request->hasFile('photo')) {
            $imageURL = $filesHandler->uploadFile($request->photo, "models/" . $color->model->MODL_NAME . '/colors/' . $color->COLR_NAME);
        }
        $editRes =  $color->editInfo(
            $request->name,
            $request->arbcName,
            $imageURL ?? $oldURL,
            $request->hex,
            $request->red,
            $request->green,
            $request->blue,
            $request->alpha
        );
        if ($editRes && $oldURL != null && $imageURL != NULL) {
            $filesHandler->deleteFile($oldURL);
        } else if (!$editRes && $imageURL != NULL) {
            $filesHandler->deleteFile($imageURL);
        }

        return back();
    }

    /////////images functions
    public function attachImage(Request $request)
    {
        $request->validate([
            "modelID"   =>  "required|exists:models,id",
            "photo"     =>  "required|file",
            "sort"     =>  "required",
        ]);
        $model = CarModel::findOrFail($request->modelID);
        $filesHandler = new FilesHandler();
        $imageURL = NULL;
        if ($request->hasFile('photo')) {
            $imageURL = $filesHandler->uploadFile($request->photo, "models/" . $model->MODL_NAME . '/images');
        }

        if (!$model->images()->create([
            "MOIM_SORT" => $request->sort,
            "MOIM_URL" => $imageURL,
        ])) {
            $filesHandler->deleteFile($imageURL);
        }

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
            "sort"     =>  "required",
        ]);
        $image = ModelImage::findOrFail($request->id);
        $oldURL = $image->MOIM_URL;
        if ($oldURL == NULL) {
            $request->validate([
                "photo"     =>  "required|file",
            ]);
        }
        $image->load("model");
        $filesHandler = new FilesHandler();

        $imageURL = NULL;
        if ($request->hasFile('photo')) {
            $imageURL = $filesHandler->uploadFile($request->photo, "models/" . $image->model->MODL_NAME . '/images');
        }

        $editRes = $image->editInfo(
            $request->name,
            $imageURL ?? $oldURL,
        );

        if ($editRes && $imageURL != NULL && $oldURL != NULL) {
            $filesHandler->deleteFile($oldURL);
        } elseif (!$editRes && $imageURL != null) {
            $filesHandler->deleteFile($imageURL);
        }

        return back();
    }

    public function attachAdjustment(Request $request)
    {
        $request->validate([
            "modelID"   =>  "required|exists:models,id",
            "name"      =>  "required",
        ]);

        ModelAdjustment::newModelAdjustment($request->modelID, $request->name, $request->desc);
        return back();
    }

    public function editAdjustment(Request $request)
    {
        $request->validate([
            "id"        =>  "required|exists:model_adjustments",
            "name"      =>  "required",
        ]);
        /** @var ModelAdjustment */
        $adj = ModelAdjustment::findOrFail($request->id);
        $adj->updateInfo($request->name, $request->desc);
        return back();
    }

    public function toggleAdjustmentState($id)
    {
        /** @var ModelAdjustment */
        $adj = ModelAdjustment::findOrFail($id);
        $adj->setActiveState(!$adj->ADJT_ACTV);
        return back();
    }

    public function addOption(Request $request)
    {
        $request->validate([
            "adjustmentID"  =>  "required|exists:model_adjustments,id",
            "name"          =>  "required",
            "image"         =>  "nullable|file",
        ]);
        /** @var ModelAdjustment */
        $adj = ModelAdjustment::with('model')->findOrFail($request->adjustmentID);
        $filesHandler = new FilesHandler();
        $imageURL = null;
        if ($request->hasFile('image')) {
            $imageURL = $filesHandler->uploadFile($request->image, "models/" . $adj->model->MODL_NAME . '/options/' . $request->name);
        }
        $adj->addOption($request->name, $imageURL, $request->desc);
        return back();
    }

    public function editOption(Request $request)
    {
        $request->validate([
            "id"            =>  "required|exists:adjustments_options",
            "name"          =>  "required",
            "image"         =>  "nullable|file",
        ]);
        /** @var AdjustmentOption */
        $option = AdjustmentOption::with('adjustment', 'adjustment.model')->findOrFail($request->id);
        $filesHandler = new FilesHandler();
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $filesHandler->uploadFile($request->image, "models/" . $option->adjustment->model->MODL_NAME . '/options/' . $request->name);
        }
        $option->updateInfo($request->name, $imagePath, $request->desc);
        return back();
    }

    public function toggleOptionState($id)
    {
        /** @var AdjustmentOption */
        $option = AdjustmentOption::findOrFail($id);
        $option->setState(!$option->ADOP_ACTV);
        return back();
    }

    public function setOptionDefault($id)
    {
        /** @var AdjustmentOption */
        $option = AdjustmentOption::findOrFail($id);
        $option->setDefault();
        return back();
    }

    //////////////////// Data functions
    private function initProfileArr($modelID)
    {
        $this->data['model'] = CarModel::with('cars', 'type', 'brand', 'colors', 'images', 'adjustments', 'adjustments.options')->findOrFail($modelID);
        //Model Categories
        $this->data['items'] = $this->data['model']->cars;
        $this->data['title'] = "Available Categories";
        $this->data['subTitle'] = "Check all Available Model categories";
        $this->data['cols'] = ['Sort Value', 'Category', 'Price'];
        $this->data['atts'] = [
            'CAR_VLUE',
            ['dynamicUrl' => ['att' => 'CAR_CATG', 'val' => 'id', 'baseUrl' => 'admin/cars/profile/']],
            ['number' => ['att' => 'CAR_PRCE', 'decimals' => 0]],
        ];
    }

    private function initDataArr()
    {
        $this->data['items'] = CarModel::with("brand")->orderBy('MODL_ACTV')->get();
        $this->data['title'] = "Available Models";
        $this->data['subTitle'] = "Check all Available Models";
        $this->data['cols'] = ['Image', 'Brand', 'Name', 'Arabic', 'Year', 'Active', 'Main', 'Overview'];
        $this->data['atts'] = [
            ['assetImg' => ['att' => 'image_url']],
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
