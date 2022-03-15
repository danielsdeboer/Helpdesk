<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Interfaces\TicketContent;

class DeletedContent extends AbstractModel implements TicketContent
{
    /** @var string */
    protected $configKey = 'helpdesk.tables.deleted_contents';

    public function partial (): string
    {
        return 'helpdesk::tickets.show.content.deleted';
    }

    public function title (): string
    {
        return 'Deleted Content';
    }

    public function fill (array $attributes)
    {
        return $this;
    }

    public function save (array $options = [])
    {
         return $this;
    }
}
