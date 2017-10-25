<?php

namespace Aviator\Helpdesk\Commands;

use Illuminate\Console\Command;
use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Traits\InteractsWithUsers;

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
        $callback = null;

        /*
         * TODO: Make this its own function.
         */
        if ($callbackClass = config('helpdesk.callbacks.user')) {
            /** @var \Aviator\Helpdesk\Interfaces\HasUserCallback $class */
            $class = new $callbackClass;
            $callback = $class->getUserCallback();
        }

        $user = $this->userModelName::query()
            ->where([
                $this->userModelEmailColumn => $this->argument('email'),
            ])
            ->when($callback, $callback)
            ->first();

        if ($user) {
            $this->updateOrCreateSuper($user);

            return;
        }

        $this->error('No user found for ' . $this->argument('email'));
    }

    /**
     * Perform the agent creation.
     * @param $user
     */
    protected function updateOrCreateSuper ($user)
    {
        Agent::query()
            ->updateOrCreate(
                ['user_id' => $user->id],
                ['is_super' => 1]
            );

        $this->info('Supervisor Agent created for ' . $this->argument('email'));
    }
}
