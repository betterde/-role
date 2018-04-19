<?php

namespace Betterde\Role\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class FlushCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flush roles cache';

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
        $redis = Redis::connection(config('role.cache.database'));
        $keys = $redis->hkeys(config('role.cache.prefix') . ':roles');
        $redis->hdel(config('role.cache.prefix') . ':roles', $keys);
        $this->info('Role cache is cleared');
    }
}
