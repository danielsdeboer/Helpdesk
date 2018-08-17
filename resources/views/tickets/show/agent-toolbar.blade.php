<div class="modal-app nav" v-cloak>
  @if ($ticket->status()->open())
    {{-- If a ticket is assigned to a particular agent, team leads and supers can still reassign --}}
    @if ($ticket->status()->assignedToAnAgent())
      @if (auth()->user()->is_super || auth()->user()->agent->teamLeads->count())
        @include('helpdesk::partials.toolbar.item', [
          'text' => 'Reassign',
          'modal' => 'reassign',
          'icon' => 'person_pin_circle'
        ])
      @endif

      {{-- Collaborators are visible once a ticket has been assigned to an agent --}}
      @include('helpdesk::partials.toolbar.item', [
        'text' => 'Add Collaborator',
        'modal' => 'collab',
        'icon' => 'people'
      ])
    @else
      @include('helpdesk::partials.toolbar.item', [
        'text' => 'Assign',
        'modal' => 'assign',
        'icon' => 'person_pin_circle'
      ])
    @endif

    @include('helpdesk::partials.toolbar.item', [
      'text' => 'Add Reply',
      'modal' => 'reply',
      'icon' => 'reply'
    ])

    @include('helpdesk::partials.toolbar.item', [
      'text' => 'Close Ticket',
      'modal' => 'close',
      'icon' => 'lock_outline'
    ])

    @include('helpdesk::partials.toolbar.item', [
      'text' => 'Add Note',
      'modal' => 'note',
      'icon' => 'note_add'
    ])
  @endif

  @if ($ticket->status()->closed())
    @include('helpdesk::partials.toolbar.item', [
      'text' => 'Reopen Ticket',
      'modal' => 'open',
      'icon' => 'lock_open'
    ])
  @endif

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
    modal-name="reassign"
    modal-title="Reassign This Ticket"
    action-route="{{ route('helpdesk.tickets.assign', $ticket->id) }}"
    csrf-token="{{ csrf_token() }}"
    button-text="Reassign Ticket"
    :available-modals="modals"
    @close-modal="close"
  >
    <p class="control">
      <span class="select">
        <select name="agent_id" title="agent-id">
        @foreach ($agents as $agent)
            <option value="{{ $agent->id }}" id="agent-option-{{ $agent->id }}">{{ $agent->user->name }}</option>
          @endforeach
        </select>
      </span>
    </p>
  </form-modal>

  <form-modal
    modal-name="assign"
    modal-title="Assign This Ticket"
    action-route="{{ route('helpdesk.tickets.assign', $ticket->id) }}"
    csrf-token="{{ csrf_token() }}"
    button-text="Assign Ticket"
    :available-modals="modals"
    @close-modal="close"
  >
    <p class="control">
      <span class="select">
        <select name="agent_id" title="agent-id">
        @foreach ($agents as $agent)
            <option value="{{ $agent->id }}" id="agent-option-{{ $agent->id }}">{{ $agent->user->name }}</option>
          @endforeach
        </select>
      </span>
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

  <form-modal
    modal-name="note"
    modal-title="Add a Note"
    action-route="{{ route('helpdesk.tickets.note', $ticket->id) }}"
    csrf-token="{{ csrf_token() }}"
    button-text="Add Note"
    :available-modals="modals"
    @close-modal="close"
  >
    <p class="control">
      <textarea id='note-body' name="note_body" class="textarea" placeholder="Note Body"></textarea>
    </p>

    <p class="control">
      <label class="checkbox">
        <input type="checkbox" name="note_is_visible" value="1">
        Note is visible to the customer
      </label>
    </p>
  </form-modal>

  <form-modal
    modal-name="collab"
    modal-title="Add a Collaborator"
    action-route="{{ route('helpdesk.tickets.collab', $ticket->id) }}"
    csrf-token="{{ csrf_token() }}"
    button-text="Add Note"
    :available-modals="modals"
    @close-modal="close"
  >
    <p class="control">
      <span class="select">
        <select name="collab-id" title="collab-id">
          @if (isset($collaborators))
            @foreach($collaborators as $agent)
              <option value="{{ $agent->id }}" id="collab-option-{{ $agent->id }}">{{ $agent->user->name }}</option>
            @endforeach
          @endif
        </select>
      </span>
    </p>
  </form-modal>
</div>

@include('helpdesk::tickets.show.toolbar.script')
