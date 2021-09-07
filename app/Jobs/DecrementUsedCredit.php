<?php

namespace App\Jobs;

use App\Models\Commitment;
use App\Utils\VideoItemUtils;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class DecrementUsedCredit implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $videoItem;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $videoItem)
    {
        $this->videoItem = $videoItem;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->videoItem['session_type']) {
            return;
        }

        $fieldToDecrement = 'used_' . $this->videoItem['session_type'] . '_quantity_master';

        if ($this->videoItem['type'] == VideoItemUtils::TYPE_ITERATION) {
            $fieldToDecrement = 'used_' . $this->videoItem['session_type'] . '_quantity_iteration';
        } else if ($this->videoItem['type'] == VideoItemUtils::TYPE_RICH_CONTENT) {
            $fieldToDecrement = 'used_' . $this->videoItem['session_type'] . '_quantity_rich_content';
        }

        Commitment::where('company_id', $this->videoItem['company_id'])
            ->decrement($fieldToDecrement);
    }
}
