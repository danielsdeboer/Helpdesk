<?php

namespace Aviator\Helpdesk\Tests;

use Aviator\Helpdesk\Database\Factories\UserFactory;
use Aviator\Helpdesk\Models\Ticket;
use Aviator\Helpdesk\Traits\HasAgentRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property mixed id
 * @property string email
 * @property string name
 * @property \Aviator\Helpdesk\Models\Agent agent
 */
class User extends Authenticatable
{
    use HasAgentRelation;
    use HasFactory;
    use Notifiable;

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

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
