<?php

namespace App\Console\Commands;

use App\SlashCommandHandlers\Mipiace as MipiaceSlackHandler;
use Illuminate\Console\Command;
use Spatie\SlashCommand\Request;

class Mipiace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mipiace:daily_menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mipiace fetch daily menu';

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
     * @return mixed
     */
    public function handle()
    {
        $request = new Request();
        $mipiaceSlackHandler = new MipiaceSlackHandler($request);
        $mipiaceSlackHandler->handle($request);
    }
}
