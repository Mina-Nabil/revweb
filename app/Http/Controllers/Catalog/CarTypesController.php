<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Models\Cars\CarType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CarTypesController extends Controller
{
    protected $data;
    protected $homeURL = 'admin/types/show';

    private function initDataArr()
    {
        $this->data['items'] = CarType::all();
        $this->data['title'] = "Available Types";
        $this->data['subTitle'] = "Manage all Available Types such as: SUV - Sedan - Hatchback";
        $this->data['cols'] = ['Name', 'Arabic', 'Edit', 'Delete'];
        $this->data['atts'] = [
            'TYPE_NAME',
            'TYPE_ARBC_NAME',
            ['edit' => ['url' => 'admin/types/edit/', 'att' => 'id']],
            ['del' => ['url' => 'admin/types/delete/', 'att' => 'id', 'msg' => 'delete the car type, system will not delete if there is any model linked with the type']],
        ];
        $this->data['homeURL'] = $this->homeURL;
    }

    public function home()
    {
        $this->initDataArr();
        $this->data['formTitle'] = "Add Type";
        $this->data['formURL'] = "admin/types/insert";
        $this->data['isCancel'] = false;
        return view('settings.types', $this->data);
    }

    public function edit($id)
    {
        $this->initDataArr();
        $this->data['type'] = CarType::findOrFail($id);
        $this->data['formTitle'] = "Edit Type ( " . $this->data['type']->TYPE_NAME . " )";
        $this->data['formURL'] = "admin/types/update";
        $this->data['isCancel'] = true;
        return view('settings.types', $this->data);
    }

    public function insert(Request $request)
    {

        $request->validate([
            "type"      => "required|unique:types,TYPE_NAME",
        ]);

        $type = new CarType();
        $type->TYPE_NAME = $request->type;
        $type->TYPE_ARBC_NAME = $request->arbcName;

        $type->save();
        return redirect($this->homeURL);
    }

    public function update(Request $request)
    {
        $request->validate([
            "id" => "required",
        ]);
        $type = CarType::findOrFail($request->id);

        $request->validate([
            "type" => ["required",  Rule::unique('types', "TYPE_NAME")->ignore($type->TYPE_NAME, "TYPE_NAME"),],
            "id"        => "required",
        ]);

        $type->TYPE_NAME = $request->type;
        $type->TYPE_ARBC_NAME = $request->arbcName;
        $type->save();

        return redirect($this->homeURL);
    }


    public function delete($id){
        $type = CarType::withCount('models')->findOrFail($id);
        if($type->models_count == 0){
            $type->delete();
        }
        return back();
    }
}
