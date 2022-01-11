<?php

namespace App\Http\Controllers;

use App\Models\Accessories;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccessoriesController extends Controller
{
    protected $data;
    protected $homeURL = 'admin/accessories/show';

    private function initDataArr()
    {
        $this->data['items'] = Accessories::all();
        $this->data['title'] = "Available Accessories";
        $this->data['subTitle'] = "Manage all Available Accessories/Options such as: Power Steering - ABS - Airbags";
        $this->data['cols'] = ['Name', 'Arabic', 'Edit'];
        $this->data['atts'] = [
            'ACSR_NAME',
            'ACSR_ARBC_NAME',
            ['edit' => ['url' => 'admin/accessories/edit/', 'att' => 'id']],
        ];
        $this->data['homeURL'] = $this->homeURL;
    }

    public function home()
    {
        $this->initDataArr();
        $this->data['formTitle'] = "Add Accessory";
        $this->data['formURL'] = "admin/accessories/insert";
        $this->data['isCancel'] = false;
        return view('settings.accessories', $this->data);
    }

    public function edit($id)
    {
        $this->initDataArr();
        $this->data['accessory'] = Accessories::findOrFail($id);
        $this->data['formTitle'] = "Edit Accessory ( " . $this->data['accessory']->ACSR_NAME . " )";
        $this->data['formURL'] = "admin/accessories/update";
        $this->data['isCancel'] = true;
        return view('settings.accessories', $this->data);
    }

    public function insert(Request $request)
    {

        $request->validate([
            "name"      => "required|unique:accessories,ACSR_NAME",
            "arbcName"      => "required",
        ]);
        try {
            $accessory = Accessories::create($request->name, $request->arbcName);
        } catch (Exception $e) {
        }

        return redirect($this->homeURL);
    }

    public function update(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);
        $accessory = Accessories::findOrFail($request->id);

        $request->validate([
            "name" => ["required",  Rule::unique('accessories', "ACSR_NAME")->ignore($accessory->ACSR_NAME, "ACSR_NAME"),],
            "arbcName"      => "required",
        ]);
        try {
            $accessory->updateInfo($request->name, $request->arbcName);
        } catch (Exception $e) {
        }
        return redirect($this->homeURL);
    }
}
