<?php

namespace Larapress\VOD\Commands;

use Illuminate\Console\Command;
use Larapress\ECommerce\Models\ProductType;

class VODCreateProductType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:vod:create-pt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create VOD product types';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ProductType::updateOrCreate([
            'name' => config('larapress.vod.product_typename'),
            'author_id' => 1,
        ], [
            'flags' => 0,
            'data' => [
                "form" => [
                ],
                "title" => trans('larapress::vod.product_type.title'),
                "agent" => "pages.vuetify.1.0"
            ]
        ]);
        $this->info("Done.");

        return 0;
    }
}
