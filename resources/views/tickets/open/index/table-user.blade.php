<table class="table">
  <thead>
  <tr>
    <th>Ticket Name</th>
    <th>Created</th>
    <th>Assigned To</th>
  </tr>
  <tbody>
  @foreach($tickets as $ticket)
    <tr>
      <td>
        <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">
          {{ str_limit($ticket->content->title(), 50) }}
        </a>
      </td>

      <td>
        {{ $ticket->created_at->format('Y-m-d') }}
      </td>

      <td>
        {{ $ticket->assignment->assignee->user->name ?? 'No One Yet' }}
      </td>
    </tr>
  @endforeach
</table>