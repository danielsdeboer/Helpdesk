<table class="table">
  <thead>
    <tr>
      <th>Ticket Name</th>
      <th>Placed By</th>
      @if (isset($withOpened))
        <th>Opened</th>
      @endif
      @if (isset($withDue) || isset($withDueToday))
        <th>Due</th>
      @endif
      @if (isset($withLastAction))
        <th>Last Action</th>
      @endif
    </tr>
  <tbody>
    @foreach($tickets as $ticket)
      <tr>
        <td><a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">{{ str_limit($ticket->content->title(), 50) }}</a></td>

        <td>{{ $ticket->user->name or '(deleted)' }}</td>

        @if (isset($withOpened))
          <td>{{ $ticket->opening->created_at->diffForHumans() }}</td>
        @endif

        @if (isset($withDue))
          <td>{{ isset($ticket->dueDate->due_on) ? $ticket->dueDate->due_on->diffForHumans() : '' }}</td>
        @endif

        @if (isset($withDueToday))
          <td>Today</td>
        @endif

        @if (isset($withLastAction))
          <td>{{ $ticket->actions->last()->created_at->diffForHumans() }}</td>
        @endif
      </tr>
    @endforeach
</table>
