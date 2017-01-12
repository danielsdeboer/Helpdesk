<section class="section is-collapsed-bottom" id="modal-app">
  <div class="nav">
    <div class="nav-item has-text-centered">
      <div>
        <p class="heading">Add Agent</p>

        <span class="icon is-medium">
          <a><i class="material-icons" @click="toggle('add')">person_add</i></a>
        </span>
      </div>
    </div>
  </div>

  @include('helpdesk::admin.agents.index.modals.add')
</section>

@include('helpdesk::partials.vue')

<script>
  var app = new Vue({
    el: '.modal-app',
    data: {
      modals: {
        add: {
          visible: false,
        },
      },
      users: {!! $users or '[]' !!}
    },
    methods: {
      toggle: function(modal) {
        this.modals[modal].visible = ! this.modals[modal].visible;
      }
    }
  });
</script>