<?php

namespace Aviator\Helpdesk\Commands;

use Illuminate\Console\Command;
use Aviator\Helpdesk\Models\Agent;

class MakeSupervisor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helpdesk:make:supervisor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an agent for the supervisor';

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
        $userModel = config('helpdesk.userModel');
        $emailColumn = config('helpdesk.userModelEmailColumn');

        $user = $userModel::where($emailColumn, config('helpdesk.supervisor.email'))->first();

        if ($user && ! Agent::where('user_id', $user->id)->first()) {
            Agent::create([
                'user_id' => $user->id,
            ]);
        }
    }
}
