{{-- DELETE THE TEAM --}}
<div class="modal" v-bind:class="{
  'is-active': modals.delete.visible
}"
>
  <div class="modal-background" @click="toggle('delete')"></div>
  <div class="modal-content">
    <div class="box">
      <h1 class="title">Delete Team</h1>

      <form method="post" action="{{ route('helpdesk.admin.teams.destroy', $team->id) }}">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}

        <p class="control">
          <label class="checkbox">
            <input type="checkbox" class="checkbox" name="delete_team_confirmed" value="1">
            Do you really want to delete this team?
          </label>
        </p>

        <div class="control is-grouped">
          <p class="control">
            <button class="button is-info">Delete Team</button>
          </p>

          <p class="control">
            <button class="button is-link" @click.prevent="toggle('delete')">Cancel</button>
          </p>
        </div>
      </form>
    </div>
  </div>
  <button class="modal-close" @click="toggle('delete')"></button>
</div>
