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
    <tr id="row-{{ $loop->iteration }}">
      <td id="row-{{ $loop->iteration }}-title">
        <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">
          {{ str_limit($ticket->content->title(), 50) }}
        </a>
      </td>

      <td id="row-{{ $loop->iteration }}-user">
        {{ $ticket->user->name ?? '(Deleted User)' }}
      </td>

      <td id="row-{{ $loop->iteration }}-created">
        {{ $ticket->created_at->format('Y-m-d') }}
      </td>

      <td id="row-{{ $loop->iteration }}-closed">
        {{ $ticket->closing->created_at->format('Y-m-d') }}
      </td>

      <td id="row-{{ $loop->iteration }}-who">
        {{ $ticket->closing->user->name ?? $ticket->closing->agent->user->name }}
      </td>
    </tr>
  @endforeach
</table>
