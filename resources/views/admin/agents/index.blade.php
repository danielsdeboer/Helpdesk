@extends('helpdesk::layout.main')

@section('content')

  @include('helpdesk::partials.errors')

  @include('helpdesk::admin.tabs', [
    'adminTab' => 'agents'
  ])

  @include('helpdesk::admin.agents.index.toolbar')

  <section class="section" id="modal-disable">
    <table class="table">
      <thead>
        <th>Name</th>
        <th>Email</th>
        <th>Teams</th>
        <th></th>
      </thead>

      <tbody>
        @foreach($agents as $agent)
          <tr>
            <td class="table-has-va">
              <a href="{{ route('helpdesk.admin.agents.show', $agent->id) }}">{{ $agent->user->name }}</a>
            </td>

            <td class="table-has-va">{{ $agent->user->$email }}</td>

            <td class="table-has-va">
              @foreach($agent->teams as $team)
                @if($loop->last)
                  <a href="{{ route('helpdesk.admin.teams.show', $team->id) }}">{{ $team->name }}</a>
                @else
                  <a href="{{ route('helpdesk.admin.teams.show', $team->id) }}">{{ $team->name }}</a>,&nbsp;
                @endif
              @endforeach
            </td>
            <td>
              <button class="button" @click="toggle('disable', {{ $agent->user }})">
                Disable Agent
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    @include('helpdesk::admin.agents.index.modals.disable')
  </section>

  <script>
    var app = new Vue({
      el: '#modal-disable',
      data: {
        modals: {
          disable: {
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
      },
    });
  </script>
@endsection
