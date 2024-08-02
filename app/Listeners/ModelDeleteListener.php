<?php

namespace App\Listeners;

use App\Models\Admin\RecycleBin;

class ModelDeleteListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        //
        RecycleBin::query()->create([
            'admin_id' => $event->adminId,
            'content' => $event->content,
            'table_name' => $event->tableName,
            'table_id' => $event->tableId,
        ]);
    }
}
