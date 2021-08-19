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
            <td class="table-has-va">
              <a href="{{ route('helpdesk.admin.agents.show', $agent->id) }}">@include('helpdesk::agent-name')</a>
            </td>

            <td class="table-has-va">{{ $agent->user->$email ?? '[Deleted]' }}</td>

            <td>
              @if($agent->user)
                <button class="button" @click="toggle('enable', {{ $agent->user }})">
                  Enable Agent
                </button>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @if(!$agents->isEmpty())
      @include('helpdesk::admin.agents.index.modals.enable')
    @endif
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
        users: {!! $users ?? '[]' !!},
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
