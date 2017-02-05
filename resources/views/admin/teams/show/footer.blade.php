<footer class="card-footer" id="modal-app">
  <a class="card-footer-item" @click="toggle('add')">
    Add Agent To Team
  </a>

  <a class="card-footer-item" @click="toggle('edit')">
    Edit Team
  </a>

  <a class="card-footer-item" @click="toggle('delete')">
    Delete Team
  </a>

  @include('helpdesk::admin.teams.show.modals.add')

  @include('helpdesk::admin.teams.show.modals.edit')

  @include('helpdesk::admin.teams.show.modals.delete')
</footer>

@include('helpdesk::partials.vue')

<script>
  var app = new Vue({
    el: '#modal-app',
    data: {
      modals: {
        add: {
          visible: false,
        },
        edit: {
          visible: false,
        },
        delete: {
          visible: false,
        },
      },
      agents: {!! $agents or '[]' !!}
    },
    methods: {
      toggle: function(modal) {
        this.modals[modal].visible = ! this.modals[modal].visible;
      }
    }
  });
</script>