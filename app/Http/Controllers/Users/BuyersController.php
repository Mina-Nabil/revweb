<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Users\Buyer;

class BuyersController extends Controller
{
    protected $data;

    public function home(){
        $this->initDataArr();
        return view('layouts.simpletable', $this->data);
    }

    private function initDataArr()
    {
        $this->data['items'] = Buyer::all();
        $this->data['title'] = "Revmo Buyers";
        $this->data['subTitle'] = "Show All Registered Clients";
        $this->data['cols'] = [ 'Full Name', 'Email', 'Mob1#', 'Mob2#'];
        $this->data['atts'] = [
            ['dynamicUrl' => ['att' => 'BUYR_NAME', 'val' => 'id', 'baseUrl' => 'admin/buyers/show/']],
            ['verified' => ['att' => 'BUYR_MAIL', 'isVerified' => 'BUYR_MAIL_VRFD']],
            ['verified' => ['att' => 'BUYR_MOB1', 'isVerified' => 'BUYR_MOB1_VRFD']],
            ['verified' => ['att' => 'BUYR_MOB2', 'isVerified' => 'BUYR_MOB2_VRFD']],
        ];
        $this->data['homeURL'] = 'admin/buyers/show';
    }
}
