<?php

namespace Database\Seeders;

use App\Models\Manager;
use App\Models\Order\Order;
use Illuminate\Database\Seeder;

class ManagerOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // get all managers
        $managers = Manager::all();

        // get all orders
        $orders = Order::all();

        $orders->each(function ($order, $key) use ($managers) {
            $order->managers()->sync($managers->random(rand(1, 3))->pluck('id'));
        });
    }
}
