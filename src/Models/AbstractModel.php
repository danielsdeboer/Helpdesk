<?php

namespace Aviator\Helpdesk\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
    use HasFactory;

    /** @var string */
    protected $configKey;

    /**
     * Set the table name from the Helpdesk config.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($this->configKey) {
            $this->setTable(
                config($this->configKey)
            );
        }
    }

    protected static function newFactory()
    {
        $modelClass = class_basename(static::class);
        $factoryClass = "Aviator\\Helpdesk\\Database\\Factories\\{$modelClass}Factory";

        return $factoryClass::new();
    }
}
