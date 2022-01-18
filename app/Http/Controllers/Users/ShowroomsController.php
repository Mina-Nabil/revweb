<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\Showroom;

;
use Illuminate\Http\Request;

class ShowroomsController extends Controller
{
    public function home(){
        $this->initDataArr();
        return view('layouts.simpletable', $this->data);
    }

    private function initDataArr()
    {
        $this->data['items'] = Showroom::all();
        $this->data['title'] = "Revmo Showrooms";
        $this->data['subTitle'] = "Show All Registered Showroom";
        $this->data['cols'] = [ 'Name', 'Email', 'Mob1#', 'Mob2#'];
        $this->data['atts'] = [
            ['dynamicUrl' => ['att' => 'SHRM_NAME', 'val' => 'id', 'baseUrl' => 'admin/showroom/show/']],
            ['verified' => ['att' => 'SHRM_MAIL', 'isVerified' => 'SHRM_MAIL_VRFD']],
            ['verified' => ['att' => 'SHRM_MOB1', 'isVerified' => 'SHRM_MOB1_VRFD']],
            ['verified' => ['att' => 'SHRM_MOB2', 'isVerified' => 'SHRM_MOB2_VRFD']],
        ];
        $this->data['homeURL'] = 'admin/buyers/show';
    }
}
