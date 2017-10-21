<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Traits\HasAgentRelation;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

/**
 * @property mixed id
 */
class User extends Model implements AuthorizableContract, AuthenticatableContract
{
    use Authorizable, Authenticatable, HasAgentRelation;

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
