<?php

namespace Aviator\Helpdesk\Commands;

use Aviator\Helpdesk\Traits\InteractsWithUsers;
use Illuminate\Console\Command;
use Aviator\Helpdesk\Models\Agent;

class CreateSuper extends Command
{
    use InteractsWithUsers;

    /** @var string */
    protected $signature = 'helpdesk:super {email}';

    /** @var string */
    protected $description = 'Create an agent for the supervisor';

    /**
     * Constructor.
     */
    public function __construct ()
    {
        parent::__construct();
        $this->setUserConfig();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        /** @var \Aviator\Helpdesk\Tests\User $user */
        $user = $this->userModelName::query()->where([
           $this->userModelEmailColumn => $this->argument('email')
        ])->first();

        if ($user) {
            $this->createSuper($user);
            return;
        }

        $this->error('No user found for ' . $this->argument('email'));
    }

    /**
     * Perform the agent creation.
     * @param $user
     */
    protected function createSuper ($user)
    {
        Agent::query()->create([
            'user_id' => $user->id,
            'is_super' => 1,
        ]);

        $this->info('Supervisor Agent created for ' . $this->argument('email'));
    }
}
