<?php

namespace Aviator\Helpdesk\Repositories;

use Aviator\Helpdesk\Models\Agent;
use Aviator\Helpdesk\Models\Team;

class EnabledAgentsRepository extends Repository
{
    /** @var array */
    protected $relations = [
        'user',
        'teams',
    ];

    /** @var string */
    protected $orderByColumn = 'user_name';

    /** @var string */
    protected $orderByDirection = 'asc';

    /** @var string */
    protected $agentsTable;

    /**
     * Constructor.
     * @param Agent $model
     */
    public function __construct (Agent $model)
    {
        $this->query = $model::query();
        $this->agentsTable = $model->getTable();
        $this->addJoins();
        $this->enabledAgents();
    }

    /**
     * Get all the agents in the given team.
     * @param Team $team
     * @return EnabledAgentsRepository
     */
    public function inTeam (Team $team)
    {
        $this->addScope('inTeam', $team);

        return $this;
    }

    /**
     * Scope the query to exclude the currently signed-in agent.
     * @return $this
     */
    public function exceptAuthorized ()
    {
        $this->addScope('exceptAuthorized');

        return $this;
    }

    private function addJoins ()
    {
        $userModel = config('helpdesk.userModel');
        /** @noinspection PhpUndefinedMethodInspection */
        $table = (new $userModel)->getTable();

        $this->query->join(
            $table,
            $this->agentsTable . '.user_id',
            '=',
            $table . '.id'
        );

        $this->query->select([
           $this->agentsTable . '.*',
           $table . '.name as user_name',
           $table . '.email as user_email',
        ]);
    }

    /**
     * Selects only enabled agents to be listed within active lists.
     * @return void
     */
    private function enabledAgents ()
    {
        $this->query->whereNull($this->agentsTable . '.is_disabled');
    }
}
