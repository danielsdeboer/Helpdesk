<table class="table">
  <thead>
  <tr>
    <th>Ticket Name</th>
    <th>Created</th>
    <th>Closed</th>
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

      <td>
        {{ $ticket->created_at->format('Y-m-d') }}
      </td>


      <td>
        {{ $ticket->closing->created_at->format('Y-m-d') }}
      </td>

      <td>
        {{ $ticket->closing->agent->user->name ?? 'You' }}
      </td>
    </tr>
  @endforeach
</table>