<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Offers\Offer;
use App\Models\Users\JoinRequest;
use App\Models\Users\Seller;

class SellersController extends Controller
{
    protected $data;

    public function home()
    {
        $this->initDataArr();
        return view('layouts.simpletable', $this->data);
    }

    public function details($id)
    {
        $this->initProfileArr($id);
        return view('stakeholders.seller_details', $this->data);
    }

    private function initProfileArr($seller_id)
    {
        $this->data['seller'] = Seller::with('showroom', 'joinRequests')->findOrFail($seller_id);
        $this->data['offersList'] = $this->data['seller']->offers;
        $this->data['offersCols'] = ['Date', 'Car', 'Buyer', 'Price', 'Status', 'Expiry', 'Comment'];
        $this->data['offersAtts'] = [
            ['date' => ['att' => 'OFFR_STRT_DATE']],
            ['foreignUrl' => ['rel' => 'showroom', 'att' => 'SHRM_NAME', 'baseUrl' => 'admin/showrooms/show', 'urlAtt' => 'id']],
            ['foreignUrl' => ['rel' => 'showroom', 'att' => 'SHRM_NAME', 'baseUrl' => 'admin/showrooms/show', 'urlAtt' => 'id']],
            [
                'state' => [
                    "att"   =>  "OFFR_STTS",
                    "text" => [
                        Offer::NEW_KEY => "New",
                        Offer::ACCEPTED_KEY => "Accepted",
                        Offer::DECLINED_KEY => "Declined",
                        Offer::EXPIRED_KEY => "Expired",
                    ],
                    "classes" => [
                        Offer::NEW_KEY => "label-info",
                        Offer::ACCEPTED_KEY => "label-success",
                        Offer::DECLINED_KEY => "label-danger",
                        Offer::EXPIRED_KEY => "label-dark",
                    ],
                ]
            ],
            ['number' => ['att' => 'OFFR_PRCE']],
            ['number' => ['att' => 'OFFR_MIN_PYMT']],
            ['date' => ['att' => 'OFFR_EXPR_DATE']],
            ['comment' => ['title' => "Sales Comment", 'att' => 'OFFR_BUYR_CMNT']],
        ];
        $this->data['joinRequestsList'] = $this->data['seller']->joinRequests;
        $this->data['joinRequestsCols'] = ['Date', 'Showroom','State'];
        $this->data['joinRequestsAtts'] = [
            ['date' => ['att' => 'created_at']],
            ['foreignUrl' => ['rel' => 'showroom', 'att' => 'SHRM_NAME', 'baseUrl' => 'admin/showrooms/show', 'urlAtt' => 'id']],
            [
                'state' => [
                    "att"   =>  "JNRQ_STTS",
                    "text" => [
                        JoinRequest::REQ_BY_SELLER => JoinRequest::REQ_BY_SELLER,
                        JoinRequest::REQ_BY_SHOWROOM => JoinRequest::REQ_BY_SHOWROOM,
                        JoinRequest::ACCEPTED => JoinRequest::ACCEPTED,
               
                    ],
                    "classes" => [
                        JoinRequest::REQ_BY_SELLER => "label-info",
                        JoinRequest::REQ_BY_SHOWROOM => "label-dark",
                        JoinRequest::ACCEPTED => "label-success",
                    ],
                ]
            ],
        ];
    }

    private function initDataArr()
    {
        $this->data['items'] = Seller::with('showroom')->get();
        $this->data['title'] = "Revmo Sellers";
        $this->data['subTitle'] = "Show All Registered Sellers";
        $this->data['cols'] = ['Showroom', 'Full Name', 'Email', 'Mob1#', 'Mob2#'];
        $this->data['atts'] = [
            ['foreignUrl' => ['rel' => 'showroom', 'att' => 'SHRM_NAME', 'baseUrl' => 'admin/showrooms/show', 'urlAtt' => 'SLLR_SHRM_ID']],
            ['dynamicUrl' => ['att' => 'SLLR_NAME', 'val' => 'id', 'baseUrl' => 'admin/sellers/show/']],
            ['verified' => ['att' => 'SLLR_MAIL', 'isVerified' => 'SLLR_MAIL_VRFD']],
            ['verified' => ['att' => 'SLLR_MOB1', 'isVerified' => 'SLLR_MOB1_VRFD']],
            ['verified' => ['att' => 'SLLR_MOB2', 'isVerified' => 'SLLR_MOB2_VRFD']],
        ];
        $this->data['homeURL'] = 'admin/sellers/show';
    }
}
