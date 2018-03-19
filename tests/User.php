<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;
use Illuminate\Notifications\Notifiable;
use Aviator\Helpdesk\Traits\HasAgentRelation;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property mixed id
 * @property string email
 * @property \Aviator\Helpdesk\Models\Agent agent
 */
class User extends Authenticatable
{
    use HasAgentRelation, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email',
    ];

    public $timestamps = false;

    protected $table = 'users';

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
