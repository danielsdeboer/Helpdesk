<div class="modal-app nav has-margin-bottom">
  @if ($ticket->status()->open())
    @include('helpdesk::partials.toolbar.item', [
      'text' => 'Close Ticket',
      'modal' => 'close',
      'icon' => 'lock_outline'
    ])

    @include('helpdesk::partials.toolbar.item', [
      'text' => 'Add Reply',
      'modal' => 'reply',
      'icon' => 'reply'
    ])
  @endif

  @if ($ticket->status()->closed())
    @include('helpdesk::partials.toolbar.item', [
      'text' => 'Reopen Ticket',
      'modal' => 'open',
      'icon' => 'lock_open'
    ])
  @endif

  {{-- Ticket Closing --}}

  <form-modal
    modal-name="close"
    modal-title="Close This Ticket"
    action-route="{{ route('helpdesk.tickets.close', $ticket->id) }}"
    csrf-token="{{ csrf_token() }}"
    button-text="Close Ticket"
    :available-modals="modals"
    @close-modal="close"
  >
    <p class="control">
      <textarea name="note" class="textarea" placeholder="Closing note (optional)"></textarea>
    </p>
  </form-modal>

  <form-modal
    modal-name="open"
    modal-title="Reopen This Ticket"
    action-route="{{ route('helpdesk.tickets.open', $ticket->id) }}"
    csrf-token="{{ csrf_token() }}"
    button-text="Open Ticket"
    :available-modals="modals"
    @close-modal="close"
  >
    <p class="control">
      <textarea name="note" class="textarea" placeholder="Note (optional)"></textarea>
    </p>
  </form-modal>

  <form-modal
    modal-name="reply"
    modal-title="Add a Reply"
    action-route="{{ route('helpdesk.tickets.reply', $ticket->id) }}"
    csrf-token="{{ csrf_token() }}"
    button-text="Add Reply"
    :available-modals="modals"
    @close-modal="close"
  >
    <p class="control">
      <textarea name="reply_body" class="textarea" placeholder="Reply Body"></textarea>
    </p>
  </form-modal>
</div>

@include('helpdesk::tickets.show.toolbar.script')