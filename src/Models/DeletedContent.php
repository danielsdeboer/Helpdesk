<?php

namespace Aviator\Helpdesk\Models;

use Aviator\Helpdesk\Interfaces\TicketContent;
use Illuminate\Database\Eloquent\Model;

class DeletedContent extends Model implements TicketContent
{
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
