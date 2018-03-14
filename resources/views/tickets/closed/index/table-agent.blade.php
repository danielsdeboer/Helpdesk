<table class="table">
  <thead>
  <tr>
    <th>Ticket Name</th>
    <th>Placed By</th>
    <th>Created At</th>
    <th>Closed At</th>
    <th>Closed By</th>
  </tr>
  <tbody>
  @foreach($tickets as $ticket)
    <tr>
      <td>
        <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">
          {{ str_limit($ticket->content->title(), 50) }}
        </a>
      </td>

      <td>{{ $ticket->user->name ?? '(Deleted User)' }}</td>

      <td>{{ $ticket->created_at->format('Y-m-d') }}</td>

      <td>{{ $ticket->closig->created_at->format('Y-m-d') }}</td>

      <td>{{ $ticket->closing->user->name ?? 'You' }}</td>
    </tr>
  @endforeach
</table>