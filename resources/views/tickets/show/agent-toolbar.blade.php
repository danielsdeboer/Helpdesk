@if (auth()->user())
  <div class="section {{ $withClass or '' }}" id="ticket-toolbar">
    <div class="container">
      <div class="modal-app nav has-margin-bottom">

        @if ($ticket->status()->open() && isset($withAssign))
          @include('helpdesk::partials.toolbar.item', [
            'text' => 'Assign',
            'modal' => 'assign',
            'icon' => 'person_pin_circle'
          ])
        @endif

        @if ($ticket->status()->open() && isset($withReply))
          @include('helpdesk::partials.toolbar.item', [
            'text' => 'Add Reply',
            'modal' => 'reply',
            'icon' => 'reply'
          ])
        @endif

        @if ($ticket->status()->open() && isset($withNote))
          @include('helpdesk::partials.toolbar.item', [
            'text' => 'Add Note',
            'modal' => 'note',
            'icon' => 'note_add'
          ])
        @endif

        @if ($ticket->status()->open() && isset($withClose))
          @include('helpdesk::partials.toolbar.item', [
            'text' => 'Close Ticket',
            'modal' => 'close',
            'icon' => 'lock_outline'
          ])
        @endif

        @if ($ticket->status()->closed() && isset($withOpen))
          @include('helpdesk::partials.toolbar.item', [
            'text' => 'Reopen Ticket',
            'modal' => 'open',
            'icon' => 'lock_open'
          ])
        @endif

        @if ($ticket->status()->open() && isset($withCollab))
          @include('helpdesk::partials.toolbar.item', [
            'text' => 'Add Collaborator',
            'modal' => 'collab',
            'icon' => 'people'
          ])
        @endif

        @if (isset($for) && $for === 'agent')
          @include('helpdesk::tickets.show.toolbar.assign')
        @endif

        {{-- CLOSE A TICKET --}}
        <div class="modal" v-bind:class="{
          'is-active': modals.close.visible
        }" v-if="modals.close.visible"
        >
          <div class="modal-background" @click="toggle('close')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Close This Ticket</h1>

              <form method="post" action="{{ route('helpdesk.tickets.close', $ticket->id) }}">
                {{ csrf_field() }}

                <p class="control">
                  <textarea name="note" class="textarea" placeholder="Closing note (optional)"></textarea>
                </p>

                <div class="control is-grouped">
                  <p class="control">
                    <button class="button is-primary" name="close_submit">Close Ticket</button>
                  </p>

                  <p class="control">
                    <button class="button is-link" @click.prevent="toggle('close')">Cancel</button>
                  </p>
                </div>
              </form>
            </div>
          </div>
          <button class="modal-close" @click="toggle('close')"></button>
        </div>

        {{-- REPLY TO A TICKET --}}
        <div class="modal" v-bind:class="{
          'is-active': modals.reply.visible
        }" v-if="modals.reply.visible"
        >
          <div class="modal-background" @click="toggle('reply')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Reply To This Ticket</h1>

              <form method="post" action="{{ route('helpdesk.tickets.reply', $ticket->id) }}">
                {{ csrf_field() }}

                <p class="control">
                  <textarea name="reply_body" class="textarea" placeholder="Reply Body"></textarea>
                </p>

                <div class="control is-grouped">
                  <p class="control">
                    <button class="button is-primary" name="reply_submit">Reply To Ticket</button>
                  </p>

                  <p class="control">
                    <button class="button is-link" @click.prevent="toggle('reply')">Cancel</button>
                  </p>
                </div>
              </form>
            </div>
          </div>
          <button class="modal-close" @click="toggle('reply')"></button>
        </div>

        {{-- ADD A NOTE TO A TICKET --}}
        <div class="modal" v-bind:class="{
          'is-active': modals.note.visible
        }" v-if="modals.note.visible"
        >
          <div class="modal-background" @click="toggle('note')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Add A Note To This Ticket</h1>

              <form method="post" action="{{ route('helpdesk.tickets.note', $ticket->id) }}">
                {{ csrf_field() }}

                <p class="control">
                  <textarea id='note-body' name="note_body" class="textarea" placeholder="Note Body"></textarea>
                </p>

                <p class="control">
                  <label class="checkbox">
                    <input type="checkbox" name="note_is_visible" value="1">
                    Note is visible to the customer
                  </label>
                </p>

                <div class="control is-grouped">
                  <p class="control">
                    <button class="button is-primary" name="note_submit">Add Note</button>
                  </p>

                  <p class="control">
                    <button class="button is-link" @click.prevent="toggle('note')">Cancel</button>
                  </p>
                </div>
              </form>
            </div>
          </div>
          <button class="modal-close" @click="toggle('note')"></button>
        </div>

        {{-- OPEN A TICKET --}}
        <div class="modal" v-bind:class="{
          'is-active': modals.open.visible
        }" v-if="modals.open.visible"
        >
          <div class="modal-background" @click="toggle('open')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Open This Ticket</h1>

              <form method="post" action="{{ route('helpdesk.tickets.open', $ticket->id) }}">
                {{ csrf_field() }}

                <p class="control">
                  <textarea name="note" class="textarea" placeholder="Note (optional)"></textarea>
                </p>

                <div class="control is-grouped">
                  <p class="control">
                    <button class="button is-primary" name="open_submit">Open Ticket</button>
                  </p>

                  <p class="control">
                    <button class="button is-link" @click.prevent="toggle('open')">Cancel</button>
                  </p>
                </div>
              </form>
            </div>
          </div>
          <button class="modal-close" @click="toggle('open')"></button>
        </div>

        @if (isset($for) && $for === 'agent')
          @include('helpdesk::tickets.show.toolbar.collab')
        @endif
    </div>
  </div>
@endif

@if(config('app.debug'))
  <script src="https://unpkg.com/vue/dist/vue.js"></script>
@else
  <script src="https://unpkg.com/vue/dist/vue.min.js"></script>
@endif

<script>
  var app = new Vue({
    el: '.modal-app',

    data: {
      modals: {
        close: {
          visible: false,
        },
        assign: {
          visible: false,
        },
        reply: {
          visible: false,
        },
        note: {
          visible: false,
        },
        open: {
          visible: false,
        },
        collab: {
          visible: false,
        }
      },
    },

    methods: {
      toggle: function(modal) {
        this.modals[modal].visible = ! this.modals[modal].visible;
      }
    }
  });
</script>
