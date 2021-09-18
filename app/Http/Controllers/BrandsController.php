<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandsController extends Controller
{
    protected $data;
    protected $homeURL = 'admin/brands/show';

    private function initDataArr()
    {
        $this->data['items'] = Brand::all();
        $this->data['title'] = "Available Brands";
        $this->data['subTitle'] = "Manage all Available Brands that should appear on this website such as Peugeot";
        $this->data['cols'] = ['Logo', 'Name', 'Arabic', 'Active', 'Edit', 'Delete'];
        $this->data['atts'] = [
            ['assetImg' => ['att' => 'BRND_LOGO']],
            'BRND_NAME',
            'BRND_ARBC_NAME',
            [
                'toggle' => [
                    "att"   =>  "BRND_ACTV",
                    "url"   =>  "admin/brands/toggle/",
                    "states" => [
                        "1" => "True",
                        "0" => "False",
                    ],
                    "actions" => [
                        "1" => "disable the brand",
                        "0" => "activate the brand, please make sure a logo is attached",
                    ],
                    "classes" => [
                        "1" => "label-success",
                        "0" => "label-danger",
                    ],
                ]
            ],
            ['edit' => ['url' => 'admin/brands/edit/', 'att' => 'id']],
            ['del' => ['url' => 'admin/brands/delete/', 'att' => 'id', 'msg' => 'delete the brand, system will not delete if there is any model linked with the brand']],
        ];
        $this->data['homeURL'] = $this->homeURL;
    }

    public function home()
    {
        $this->initDataArr();
        $this->data['formTitle'] = "Add Brand";
        $this->data['formURL'] = "admin/brands/insert";
        $this->data['isCancel'] = false;
        return view('settings.brands', $this->data);
    }

    public function edit($id)
    {
        $this->initDataArr();
        $this->data['brand'] = Brand::findOrFail($id);
        $this->data['formTitle'] = "Edit Brand ( " . $this->data['brand']->BRND_NAME . " )";
        $this->data['formURL'] = "admin/brands/update";
        $this->data['isCancel'] = true;
        return view('settings.brands', $this->data);
    }

    public function insert(Request $request)
    {

        $request->validate([
            "name"      => "required|unique:brands,BRND_NAME",
            "logo"      => "required_if:isActive,on"
        ]);

        $brand = new Brand();
        $brand->BRND_NAME = $request->name;
        $brand->BRND_ARBC_NAME = $request->arbcName;
        if ($request->hasFile('logo')) {
            $brand->BRND_LOGO = $request->logo->store('images/brands/' . $brand->BRND_NAME, 'public');
        }
        $brand->BRND_ACTV = $request->isActive == 'on' ? 1 : 0;

        $brand->save();
        return redirect($this->homeURL);
    }

    public function update(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);
        $brand = Brand::findOrFail($request->id);

        $request->validate([
            "name" => ["required",  Rule::unique('brands', "BRND_NAME")->ignore($brand->BRND_NAME, "BRND_NAME"),],
            "id"        => "required",
            "logo"      => "required_if:isActive,on"
        ]);

        $brand->BRND_NAME = $request->name;
        $brand->BRND_ARBC_NAME = $request->arbcName;
        if ($request->hasFile('logo')) {
            $this->deleteOldBrandPhoto($brand->BRND_LOGO);
            $brand->BRND_LOGO = $request->logo->store('images/brands/' . $brand->BRND_NAME, 'public');
        }
        $brand->BRND_ACTV = $request->isActive == 'on' ? 1 : 0;
        $brand->save();

        return redirect($this->homeURL);
    }

    public function toggle($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->toggle();
        return back();
    }

    public function delete($id){
        $brand = Brand::withCount('models')->findOrFail($id);
        if($brand->models_count == 0){
            $brand->delete();
        }
        return back();
    }

    private function deleteOldBrandPhoto($brandFilePath)
    {
        if (isset($brandFilePath) && $brandFilePath != '') {
            try {
                unlink(public_path('storage/' . $brandFilePath));
            } catch (Exception $e) {
            }
        }
    }
}
