<div class="modal" v-bind:class="{
  'is-active': modals.add.visible
}"
>
  <div class="modal-background" @click="toggle('add')"></div>
  <div class="modal-content">
    <div class="box">
      <h1 class="title">Add A New Agent</h1>

      <form method="post" action="{{ route('helpdesk.admin.agents.store') }}">
        {{ csrf_field() }}

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
            <button class="button is-info">Add Agent</button>
          </p>

          <p class="control">
            <button
              class="button is-link"
              name="user_submit"
              @click.prevent="toggle('add')"
            >Cancel</button>
          </p>
        </div>
      </form>
    </div>
  </div>

  <button class="modal-close" @click="toggle('add')"></button>
</div>
