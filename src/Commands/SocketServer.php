<?php

namespace Chunrongl\TqigouService\Commands;


use Illuminate\Console\Command;

class SocketServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tqigou:server:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '淘气购rpc server';

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
        $server = app('tqigou.server');

        $server->start();
    }
}