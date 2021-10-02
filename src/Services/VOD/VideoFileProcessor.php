<?php

namespace Larapress\VOD\Services\VOD;

use Illuminate\Http\Request;
use Larapress\FileShare\Models\FileUpload;
use Larapress\FileShare\Services\FileUpload\ScheduledFileProcessor;
use Larapress\Reports\Models\TaskReport;
use Larapress\Reports\Services\TaskScheduler\ITaskHandler;

class VideoFileProcessor extends ScheduledFileProcessor implements ITaskHandler
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param FileUpload $upload
     * @return string
     */
    public function getTaskClass(Request $request, FileUpload $upload): string
    {
        return self::class;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param FileUpload $upload
     * @return string
     */
    public function getTaskName(Request $request, FileUpload $upload): string
    {
        return 'convert-' . $upload->id;
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param FileUpload $upload
     * @return string
     */
    public function getTaskDescription(Request $request, FileUpload $upload): string
    {
        return trans('larapress::vod.convert_task', [
            'id' => $upload->id,
        ]);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param FileUpload $upload
     * @return array
     */
    public function getTaskData(Request $request, FileUpload $upload): array
    {
        return [];
    }

    /**
     * Undocumented function
     *
     * @param FileUpload $upload
     * @return boolean
     */
    public function shouldProcessFile(FileUpload $upload)
    {
        return \Illuminate\Support\Str::startsWith($upload->mime, 'video/');
    }

    /**
     * Undocumented function
     *
     * @param TaskReport $task
     * @return void
     */
    public function handle(TaskReport $task)
    {
        VideoConvertJob::dispatch($task->data['id']);
    }
}
