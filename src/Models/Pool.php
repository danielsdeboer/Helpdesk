<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pool extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /**
     * Set the table name from the Helpdesk config
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('helpdesk.tables.pools'));
    }

    public function agents() {
        return $this->belongsToMany(Agent::class, config('helpdesk.tables.agent_pool'))->withPivot('is_team_lead')->withTimestamps();
    }

    public function teamLeads() {
        return $this->belongsToMany(Agent::class, config('helpdesk.tables.agent_pool'))
            ->withPivot('is_team_lead')
            ->withTimestamps()
            ->wherePivot('is_team_lead', 1);
    }


}