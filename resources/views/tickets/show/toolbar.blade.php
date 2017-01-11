<div class="section {{ $withClass or '' }}">
  <div class="container">
    <div class="modal-app nav">

      @if ($ticket->isOpen() && isset($withAssign))
        <div class="nav-item has-text-centered">
          <div>
            <p class="heading">Assign</p>

            <a @click="toggle('assign')">
              <span class="icon">
                <i class="material-icons">person_pin_circle</i>
              </span>
            </a>
          </div>
        </div>
      @endif

      @if ($ticket->isOpen() && isset($withReply))
        <div class="nav-item has-text-centered">
          <div>
            <p class="heading">Add Reply</p>

            <a @click="toggle('reply')">
              <span class="icon">
                <i class="material-icons">reply</i>
              </span>
            </a>
          </div>
        </div>
      @endif


      @if ($ticket->isOpen() && isset($withNote))
        <div class="nav-item has-text-centered">
          <div>
            <p class="heading">Add Note</p>

            <a @click="toggle('note')">
              <span class="icon">
                <i class="material-icons">note_add</i>
              </span>
            </a>
          </div>
        </div>
      @endif

      @if ($ticket->isOpen() && isset($withClose))
        <div class="nav-item has-text-centered">
          <div>
            <p class="heading">Close Ticket</p>

            <span class="icon">
              <a @click="toggle('close')">
                <i class="material-icons">lock_outline</i>
              </a>
            </span>
          </div>
        </div>
      @endif

      @if ($ticket->isClosed() && isset($withOpen))
        <div class="nav-item has-text-centered">
          <div>
            <p class="heading">Reopen Ticket</p>

            <span class="icon">
              <a href="#">
                <i class="material-icons">lock_open</i>
              </a>
            </span>
          </div>
        </div>
      @endif

      {{-- ASSIGN A TICKET --}}
      <div class="modal" v-bind:class="{
        'is-active': modals.assign.visible
      }" v-if="modals.assign.visible"
      >
        <div class="modal-background" @click="toggle('assign')"></div>
        <div class="modal-content">
          <div class="box">
            <h1 class="title">Assign This Ticket To An Agent</h1>

            <form method="post" action="{{ route('helpdesk.tickets.assign', $ticket->id) }}">
              {{ csrf_field() }}

              <p class="control">
                <select name="agent_id" class="select">
                  <option
                    v-for="agent in agents"
                    :value="agent.id"
                  >@{{ agent.user.name }}</option>
                </select>
              </p>

              <div class="control is-grouped">
                <p class="control">
                  <button class="button is-primary">Assign Ticket</button>
                </p>

                <p class="control">
                  <button class="button is-link" @click.prevent="toggle('assign')">Cancel</button>
                </p>
              </div>
            </form>
          </div>
        </div>
        <button class="modal-close" @click="toggle('assign')"></button>
      </div>

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
                  <button class="button is-primary">Close Ticket</button>
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
                <textarea name="body" class="textarea" placeholder="Reply Body"></textarea>
              </p>

              <div class="control is-grouped">
                <p class="control">
                  <button class="button is-primary">Reply To Ticket</button>
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
                <textarea name="body" class="textarea" placeholder="Note Body"></textarea>
              </p>

              <div class="control is-grouped">
                <p class="control">
                  <button class="button is-primary">Add Note</button>
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

      {{-- CLOSE A TICKET --}}
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
                  <button class="button is-primary">Open Ticket</button>
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

    </div>
  </div>
</div>

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
        }
      },
      agents: {!! $agents or '[]' !!}
    },
    methods: {
      toggle: function(modal) {
        this.modals[modal].visible = ! this.modals[modal].visible;
      }
    }
  });
</script>