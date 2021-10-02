<?php

namespace Larapress\VOD\Services\VOD;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Larapress\FileShare\Models\FileUpload;
use Larapress\Reports\Services\TaskScheduler\ITaskSchedulerService;

use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;
use Illuminate\Support\Facades\Storage;

class VideoConvertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var FileUpload
     */
    private $uploadId;
    private $upload;

    /**
     * Create a new job instance.
     *
     * @param int $message
     */
    public function __construct(int $uploadId)
    {
        $this->uploadId = $uploadId;
        $this->upload = FileUpload::find($uploadId);
        $this->onQueue(config('larapress.vod.queue'));
    }

    public function tags()
    {
        return ['video-convert', 'video:' . $this->upload->id];
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var ITaskSchedulerService */
        $taskService = app(ITaskSchedulerService::class);

        $taskData = ['id' => $this->upload->id];
        $taskService->startSyncronizedTaskReport(
            VideoFileProcessor::class,
            'convert-' . $this->upload->id,
            trans('larapress::vod.convert_started'),
            $taskData,
            function ($onUpdate, $onSuccess, $onFailed) use ($taskData) {
                try {
                    $startTime = time();
                    $rates = config('larapress.vod.hls_variants');

                    /** @var HLSPlaylistExporter $mediaHLSExport */
                    $mediaHLSExport = FFMpeg::fromDisk($this->upload->storage)
                        ->open($this->upload->path)
                        ->exportForHLS();
                    foreach ($rates as $rate => $size) {
                        $bitRate = (new X264())
                            ->setKiloBitrate($rate)
                            ->setAudioCodec('libfdk_aac');

                        $mediaHLSExport->addFormat($bitRate, function ($media) use ($size) {
                            $media->addLegacyFilter(function ($filters) use ($size) {
                                $filters->resize(new Dimension($size[0], $size[1]));
                            });
                        });
                    }
                    $mediaHLSExport->onProgress(function ($percent) use ($onUpdate, $taskData) {
                        $onUpdate(trans('larapress::vod.convert_updated', ['percent' => $percent]), $taskData);
                    });
                    $mediaHLSExport->setSegmentLength(10);

                    $dir = substr($this->upload->path, 0, strrpos($this->upload->path, '.', -1));
                    Storage::disk($this->upload->storage)->makeDirectory($dir);
                    $mediaHLSExport->save($dir . '/vod.m3u8');
                    $took = time() - $startTime;
                    $onSuccess(trans('larapress::vod.convert_finished', ['sec' => $took]), $taskData);
                } catch (\Exception $e) {
                    $onFailed('Error: '.$e->getMessage(), $taskData);
                }
            }
        );
    }
}
