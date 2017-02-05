
{{-- EDIT THE TEAM --}}
<div class="modal" v-bind:class="{
  'is-active': modals.edit.visible
}"
>
  <div class="modal-background" @click="toggle('edit')"></div>
  <div class="modal-content">
    <div class="box">
      <h1 class="title">Edit Team</h1>

      <form method="post" action="{{ route('helpdesk.admin.teams.update', $team->id) }}">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}

        <label>Team Name</label>
        <p class="control">
          <input class="input" name="name" placeholder="Team Name" value="{{ $team->name }}">
        </p>

        <div class="control is-grouped">
          <p class="control">
            <button class="button is-primary">Edit Team</button>
          </p>

          <p class="control">
            <button class="button is-link" @click.prevent="toggle('edit')">Cancel</button>
          </p>
        </div>
      </form>
    </div>
  </div>
  <button class="modal-close" @click="toggle('edit')"></button>
</div>
