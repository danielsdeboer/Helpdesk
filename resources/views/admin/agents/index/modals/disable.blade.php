<div class="modal" v-bind:class="{
  'is-active': modals.disable.visible
}"
>
  <div class="modal-background" @click="toggle('disable', '')"></div>
  <div class="modal-content">
    <div class="box">
      <h1 class="title">Are you sure you want to disable this agent?</h1>

      <form method="POST" action="{{ route('helpdesk.admin.disabled.update', $agent->id) }}">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}

        <p class="control">
          <span class="select">
            <select name="user_id">
              <option
                v-for="user in users"
                :value="user.id"
              >@{{ user.name }}</option>
            </select>
          </span>
        </p>

        <div class="control is-grouped">
          <p class="control">
            <button class="button is-info">Disable Agent</button>
          </p>

          <p class="control">
            <button
              class="button is-link"
              name="user_submit"
              @click.prevent="toggle('disable', '')"
            >Cancel</button>
          </p>
        </div>
      </form>
    </div>
  </div>

  <button class="modal-close" @click="toggle('disable', '')"></button>
</div>
