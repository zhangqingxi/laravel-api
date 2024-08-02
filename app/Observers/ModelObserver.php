<?php

namespace App\Observers;

use App\Events\ModelDeleteEvent;
use App\Events\ModelLogEvent;
use App\Models\Admin\RecycleBin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class ModelObserver
{
    //
    /**
     * 模型新建后
     * @param Model $model
     */
    public function created(Model $model): void
    {
        $attributes = Arr::except($model->getAttributes(), ['created_at', 'updated_at']);
        $modelName = get_class($model);
        $modelId = $model->getKey();
        $content = $attributes;

        event(new ModelLogEvent($content, $modelName, $modelId));
    }


    /**
     * 模型更新后
     * @param Model $model
     */
    public function updated(Model $model): void
    {

        $dirty = Arr::except($model->getDirty(), ['updated_at']);

        $original = Arr::only($model->getOriginal(), array_keys($dirty));

        if (count($dirty) > 0) {

            $modelName = get_class($model);
            $modelId = $model->getKey();
            $content = ["before" =>  $original, "after" => $dirty];

            event(new ModelLogEvent($content, $modelName, $modelId));
        }
    }

    /**
     * 模型删除后
     * @param Model $model
     */
    public function deleted(Model $model): void
    {

        $attributes = $model->getAttributes();
        $modelName = get_class($model);
        $modelId = $model->getKey();

        // 如果不是资源回收表，将删除数据保存到回收表
        if (!($model instanceof RecycleBin)) {

            event(new ModelDeleteEvent($attributes, $modelName, $modelId));
        }
        $content = $attributes;

        event(new ModelLogEvent($content, $modelName, $modelId));
    }
}
