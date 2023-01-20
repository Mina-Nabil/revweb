<?php

namespace App\Models\Subscriptions;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    public $timestamps = false;

    ///////limits 
    const ADMINS_LIMIT = 1;
    const USERS_LIMIT = 2;
    const MODELS_LIMIT = 3;
    const OFFERS_LIMIT = 4;
    const SERVICES_LIMIT = 5;

    ////static functions
    public static function createPlan(string $name, float $monthly_price,  float $annual_price, int $admins_limit, int $users_limit, int $models_limit, int $offers_limit, int $services_limit, bool $facility_payment, bool $email_support, bool $chat_support, bool $phone_support, bool $dashboard_access, int $order)
    {
        $newPlan = new self;

        $newPlan->name = $name;
        $newPlan->monthly_price = $monthly_price;
        $newPlan->annual_price = $annual_price;
        $newPlan->admins_limit = $admins_limit;
        $newPlan->users_limit = $users_limit;
        $newPlan->models_limit = $models_limit;
        $newPlan->offers_limit = $offers_limit;
        $newPlan->services_limit = $services_limit;
        $newPlan->facility_payment = $facility_payment;
        $newPlan->email_support = $email_support;
        $newPlan->chat_support = $chat_support;
        $newPlan->phone_support = $phone_support;
        $newPlan->dashboard_access = $dashboard_access;
        $newPlan->order = $order;

        $newPlan->save();
    }

    public static function free() : self
    {
        return self::find(1);
    }
}
