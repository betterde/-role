<?php

namespace Betterde\Role\Commands;

use Exception;
use Illuminate\Console\Command;
use Betterde\Role\Contracts\RoleContract;

class CreateRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:create {code : The code of the role} {name : The name of the role} {guard? : The guard of the guard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create and save role';

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
        try {
            $role = $model::store([
                'code' => $this->argument('code'),
                'name' => $this->argument('name'),
                'guard' => $this->argument('guard')
            ]);
            $this->info("Congratulation! Role `{$role->name}` created");
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
