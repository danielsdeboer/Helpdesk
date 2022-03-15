<table class="table" data-section="tickets-table">
  <thead>
  <tr>
    <th>Ticket Title</th>
    <th>Added</th>
  </tr>
  </thead>

  <tbody>
  @if ($tickets->count() > 0)
    @foreach($tickets as $ticket)
      <tr>
        <td>
          <a
            data-ticket-id="{{ $ticket->id }}"
            data-ticket-title="{{ $ticket->getSafeContent()->title() }}"
            href="{{ route('helpdesk.tickets.show', $ticket->id) }}"
          >
            {{ $ticket->getSafeContent()->title() }}
          </a>
        </td>

        <td>
          {{ $ticket->created_at->toDateString() }}
        </td>
      </tr>
    @endforeach
  @else
    <tr>
      <td colspan="2" class="has-text-centered">
        <section class="section">
          <span class="icon is-large">
            <i class="material-icons is-mi-large">mood</i>
          </span>

          <p>Nothing to see here!</p>
        </section>
      </td>
    </tr>
  @endif
  </tbody>
</table>
</div>
