<footer class="modal-app card-footer">
  <a class="card-footer-item" @click="toggle('add')">
    Add Agent To Team
  </a>

  <a class="card-footer-item" @click="toggle('delete')">
    Delete Agent
  </a>

  <div class="modal" v-bind:class="{
    'is-active': modals.add.visible
  }"
  >
    <div class="modal-background" @click="toggle('add')"></div>
    <div class="modal-content">
      <div class="box">
        <h1 class="title">Add This Agent To A Team</h1>

        <form method="post" action="{{ route('helpdesk.admin.team-members.store', $agent->id) }}">
          {{ csrf_field() }}

          <p class="control">
            <select name="agent_id" class="select">
              <option
                v-for="team in teams"
                :value="team.id"
              >@{{ team.name }}</option>
            </select>
          </p>

          <div class="control is-grouped">
            <p class="control">
              <button class="button is-primary">Add Agent To Team</button>
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

  <div class="modal" v-bind:class="{
    'is-active': modals.delete.visible
  }"
  >
    <div class="modal-background" @click="toggle('delete')"></div>
    <div class="modal-content">
      <div class="box">
        <h1 class="title">Delete Agent</h1>

        <form method="post" action="{{ route('helpdesk.admin.agents.destroy', $agent->id) }}">
          {{ csrf_field() }}
          {{ method_field('DELETE') }}

          <p class="control">
            <label class="checkbox">
              <input type="checkbox" class="checkbox" name="delete_agent_confirmed" value="1">
              Do you really want to delete this agent?
            </label>
          </p>

          <div class="control is-grouped">
            <p class="control">
              <button class="button is-primary">Delete Agent</button>
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
</footer>

@include('helpdesk::partials.vue')

<script>
  var app = new Vue({
    el: '.modal-app',
    data: {
      modals: {
        add: {
          visible: false,
        },
        delete: {
          visible: false,
        },
      },
      teams: {!! $teams or '[]' !!}
    },
    methods: {
      toggle: function(modal) {
        this.modals[modal].visible = ! this.modals[modal].visible;
      }
    }
  });
</script>