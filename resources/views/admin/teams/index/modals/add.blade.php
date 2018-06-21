<div class="modal" v-bind:class="{
  'is-active': modals.add.visible
}"
>
  <div class="modal-background" @click="toggle('add')"></div>
  <div class="modal-content">
    <div class="box">
      <h1 class="title">Add A New Team</h1>

      <form method="post" action="{{ route('helpdesk.admin.teams.store') }}">
        {{ csrf_field() }}

        <label>Team Name</label>
        <p class="control">
          <input class="input" name="name" placeholder="Team Name">
        </p>

        <div class="control is-grouped">
          <p class="control">
            <button class="button is-info">Add Team</button>
          </p>

          <p class="control">
            <button class="button is-link" @click.prevent="toggle('add')">Cancel</button>
          </p>
        </div>
      </form>
    </div>
  </div>
  <button class="modal-close" @click="toggle('add')"></button>
</div>
