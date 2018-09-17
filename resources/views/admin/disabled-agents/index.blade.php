@extends('helpdesk::layout.main')

@section('content')

  @include('helpdesk::partials.errors')

  @include('helpdesk::admin.tabs', [
    'adminTab' => 'disabled'
  ])

  <section class="section" id="modal-enable">
    <table class="table">
      <thead>
        <th>Name</th>
        <th>Email</th>
        <th></th>
      </thead>

      <tbody>
        @foreach($agents as $agent)
          <tr>
            <td><a href="{{ route('helpdesk.admin.agents.show', $agent->id) }}">{{ $agent->user->name }}</a></td>

            <td>{{ $agent->user->$email }}</td>

            <td>
              <button class="button" @click="toggle('enable', {{ $agent->user }})">
                Enable Agent
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @include('helpdesk::admin.agents.index.modals.enable')
  </section>

  @include('helpdesk::partials.vue')

  <script>
    var app = new Vue({
      el: '#modal-enable',
      data: {
        modals: {
          enable: {
            visible: false,
          },
        },
        users: {!! $users or '[]' !!},
      },
      methods: {
        toggle: function(modal, agent) {
          this.users = [agent];
          this.modals[modal].visible = ! this.modals[modal].visible;
        },
      }
    });
  </script>
@endsection
