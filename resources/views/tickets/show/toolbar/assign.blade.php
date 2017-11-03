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
          <span class="select">
            <select name="agent_id" title="agent-id">
              @foreach ($agents as $agent)
                <option value="{{ $agent->id }}" id="agent-option-{{ $agent->id }}">{{ $agent->user->name }}</option>
              @endforeach
            </select>
          </span>
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
