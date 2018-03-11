<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
    /** @var string */
    protected $configKey;

    /**
     * Set the table name from the Helpdesk config.
     * @param array $attributes
     */
    public function __construct (array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(
            config($this->configKey)
        );
    }
}
