<div class="level-right">

  {{-- When a ticket is open, show all available statuses. When closed, only show closed. --}}

  @if ($ticket->status()->open())

    {{-- Overdue / On Time status --}}

    @if ($ticket->status()->overdue())
      @include('helpdesk::tickets.show.status.item', [
        'class' => 'is-danger',
        'text' => 'Overdue'
      ])
    @else
      @include('helpdesk::tickets.show.status.item', [
        'class' => 'is-success',
        'text' => 'On Time'
      ])
    @endif

    {{-- Assignment status --}}

    @if (!$ticket->status()->assigned())
      @include('helpdesk::tickets.show.status.item', [
        'class' => 'is-danger',
        'text' => 'Not Assigned'
      ])
    @endif

    @if ($ticket->status()->assignedToAnAgent())
      @include('helpdesk::tickets.show.status.item', [
        'class' => 'is-success',
        'text' => 'Assigned'
      ])
    @endif

    @if ($ticket->status()->assignedToATeam())
      @include('helpdesk::tickets.show.status.item', [
        'class' => 'is-warning',
        'text' => 'Assigned To Team'
      ])
    @endif
  @else
    @include('helpdesk::tickets.show.status.item', [
      'class' => 'is-info',
      'text' => 'Closed'
    ])
  @endif
</div>
