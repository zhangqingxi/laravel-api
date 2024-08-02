<?php

namespace App\Console\Commands;

use App\Models\Admin\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '删除本地无效文件';

    public function handle(): void
    {

        // 获取本地存储中的所有文件
        $localFiles = Storage::disk('admin')->allFiles();

        // 数据库匹配
        foreach ($localFiles as $file){

            if(!File::query()->where('path', $file)->exists()){

                Storage::disk('admin')->delete($file);
            }
        }
    }
}
