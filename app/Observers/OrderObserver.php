<?php

namespace App\Observers;

use App\Models\Company;
use App\Models\Order\Order;
use App\Models\Video\Video;
use App\Utils\UserUtils;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        // update the video status and pending side
        Video::where('id', $order->video_id)->update([
            'status' => 2, // pending - brief
            'pending_side' => UserUtils::USER_MANAGER
        ]);

        // get the company query
        $companyQuery = Company::where('id', function ($query) use ($order) {
            $query->select('company_id')
                ->from('videos')
                ->where('videos.id', $order->video_id)
                ->limit(1);
        });
        // increment the total orders count in companies table
        $companyQuery->increment('total_orders_count');
        // increment the active orders count in companies table
        $companyQuery->increment('active_orders_count');

        // get the manager ids of the company
        $managerIds = $companyQuery->first()->managers()->select('managers.id as id')->get()->pluck('id');
        // sync
        $order->managers()->sync($managerIds);
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
