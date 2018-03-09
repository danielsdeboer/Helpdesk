<?php

namespace Aviator\Helpdesk\Models;

/**
 * @property string body
 * @property string title
 */
class GenericContent extends ContentBase
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.generic_contents';

    /** @var array */
    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * The partial that displays this content.
     * @var string
     */
    protected $partial = 'helpdesk::tickets.show.content.generic';
}
