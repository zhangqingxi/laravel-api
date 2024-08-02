<?php
namespace App\Jobs\Admin;

use App\Models\Admin\Admin;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateLoginDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Admin $admin;
    protected string $ip;

    /**
     * Create a new job instance.
     *
     * @param Admin $admin
     * @param string $ip
     */
    public function __construct(Admin $admin, string $ip)
    {
        $this->admin = $admin;
        $this->ip = $ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->admin->login_ip = $this->ip;
        $this->admin->last_login_at = now();
        $this->admin->save();
    }
}
