<div class="modal" v-bind:class="{
  'is-active': modals.collab.visible
}" v-if="modals.collab.visible"
>
  <div class="modal-background" @click="toggle('collab')"></div>

  <div class="modal-content">
    <div class="box">
      <h1 class="title">Add a Collaborator</h1>

      <form method="post" action="{{ route('helpdesk.tickets.collab', $ticket->id) }}">
        {{ csrf_field() }}

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

        <div class="control is-grouped">
          <p class="control">
            <button class="button is-primary" name="collab_submit">Add Collaborator</button>
          </p>

          <p class="control">
            <button class="button is-link" @click.prevent="toggle('collab')">Cancel</button>
          </p>
        </div>
      </form>
    </div>
  </div>

  <button class="modal-close" @click="toggle('open')"></button>
</div>
