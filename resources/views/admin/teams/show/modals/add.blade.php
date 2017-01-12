{{-- ADD AN AGENT --}}
<div class="modal" v-bind:class="{
  'is-active': modals.add.visible
}"
>
  <div class="modal-background" @click="toggle('add')"></div>
  <div class="modal-content">
    <div class="box">
      <h1 class="title">Add An Agent To This Team</h1>

      <form method="post" action="{{ route('helpdesk.admin.team-members.store') }}">
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
            <button class="button is-primary">Add Agent</button>
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