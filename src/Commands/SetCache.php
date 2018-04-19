<?php

namespace Betterde\Role\Commands;

use Illuminate\Console\Command;
use Betterde\Role\Contracts\RoleContract;

class SetCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all role to memory';

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
        $model = app(RoleContract::class);
        $model::fetchAll();
        $this->info('Congratulation! All roles is cached');
    }
}
