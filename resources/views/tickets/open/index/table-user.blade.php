<table class="table">
  <thead>
  <tr>
    <th>Ticket Name</th>
    <th>Created</th>
    <th>Assigned To</th>
  </tr>
  <tbody>
  @foreach($tickets as $ticket)
    <tr id="row-{{ $loop->iteration }}">
      <td id="row-{{ $loop->iteration }}-title">
        <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">
          {{ str_limit($ticket->content->title(), 50) }}
        </a>
      </td>

      <td id="row-{{ $loop->iteration }}-created">
        {{ $ticket->created_at->format('Y-m-d') }}
      </td>

      <td id="row-{{ $loop->iteration }}-assignee">
        {{ $ticket->assignment->assignee->user->name ?? 'No One Yet' }}
      </td>
    </tr>
  @endforeach
</table>
