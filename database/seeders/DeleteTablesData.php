<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;
use App\Models\Contractor;
use App\Models\DeviceToken;
use App\Models\Gift;
use App\Models\GiftStore;
use App\Models\ManagersStore;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderGift;
use App\Models\Point;
use App\Models\RedeemRequest;
use App\Models\Reference;
use App\Models\Store;
use App\Models\User;

class DeleteTablesData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bank::truncate();
        Contractor::truncate();
        DeviceToken::truncate();
        Gift::truncate();
        GiftStore::truncate();
        ManagersStore::truncate();
        Notification::truncate();
        Order::truncate();
        OrderGift::truncate();
        Point::truncate();
        RedeemRequest::truncate();
        Reference::truncate();
        Store::truncate();
        User::where('role', 1)->delete();
    }
}
