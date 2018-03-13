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

{{-- CLOSE A TICKET --}}
<div
  class="modal"
  v-bind:class="{
    'is-active': modals.close.visible
  }"
  v-if="modals.close.visible"
  @keydown.esc="close('close')"
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
<div
  class="modal"
  v-bind:class="{
    'is-active': modals.reply.visible
  }"
  v-if="modals.reply.visible"
  @keydown.esc="close('reply')"
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

{{-- OPEN A TICKET --}}
<div
  class="modal"
  v-bind:class="{
    'is-active': modals.open.visible
  }"
  v-if="modals.open.visible"
  @keydown.esc="close('open')"
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
          visible: false
        },
        open: {
          visible: false
        },
        reply: {
          visible: false
        }
      }
    },

    methods: {
      toggle: function (modal) {
        this.modals[modal].visible = ! this.modals[modal].visible
      },

      close: function (modal) {
        if (this.modals[modal].visible) {
            this.toggle(modal)
        }
      }
    }
  });
</script>
