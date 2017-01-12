@extends('helpdesk::layout.main')

@section('content')
  @include('helpdesk::admin.tabs', [
    'adminTab' => 'teams'
  ])

  <section class="section">
    <div class="card">
      <div class="card-content">
        <div class="level">
          <div class="level-left">
            <div>
              <h1 class="title"><strong>{{ $team->name }}</strong></h1>
              <h2 class="subtitle">Added on {{ $team->created_at->toDateString() }}</h2>
            </div>
          </div>

          <div class="level-right">
            <div class="columns">
              <div class="column">
                <span class="tag is-info is-medium">{{ $team->agents->count() }} members</span>
              </div>

              <div class="column">
                @if ($tickets->count() == 0)
                  <span class="tag is-success is-medium">{{ $tickets->count() }} open tickets</span>
                @elseif ($tickets->count() == 1)
                  <span class="tag is-warning is-medium">{{ $tickets->count() }} open ticket</span>
                @else
                  <span class="tag is-warning is-medium">{{ $tickets->count() }} open tickets</span>
                @endif
              </div>
            </div>
          </div>
        </div>

        <table class="table">
          <thead>
            <tr>
              <th>Agent Name</th>
              <th>Added</th>
            </tr>
          </thead>

          <tbody>
            @foreach($team->agents as $agent)
              <tr>
                <td>
                  <a href="{{ route('helpdesk.admin.agents.show', $agent->id) }}">{{ $agent->user->name }}</a>
                </td>

                <td>
                  {{ $agent->pivot->created_at->toDateString() }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <table class="table">
          <thead>
            <tr>
              <th>Ticket Title</th>
              <th>Added</th>
            </tr>
          </thead>

          <tbody>
            @foreach($tickets as $ticket)
              <tr>
                <td>
                  <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">{{ $ticket->content->title }}</a>
                </td>

                <td>
                  {{ $ticket->created_at->toDateString() }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <footer class="modal-app card-footer">
        <a class="card-footer-item" @click="toggle('add')">
          Add Agent To Team
        </a>

        <a class="card-footer-item" @click="toggle('edit')">
          Edit Team
        </a>

        <a class="card-footer-item" @click="toggle('delete')">
          Delete Team
        </a>

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

        {{-- EDIT THE TEAM --}}
        <div class="modal" v-bind:class="{
          'is-active': modals.edit.visible
        }"
        >
          <div class="modal-background" @click="toggle('edit')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Edit Team</h1>

              <form method="post" action="{{ route('helpdesk.admin.teams.update', $team->id) }}">
                {{ csrf_field() }}
                {{ method_field('PATCH') }}

                <label>Team Name</label>
                <p class="control">
                  <input class="input" name="team_name" placeholder="Team Name" value="{{ $team->name }}">
                </p>

                <div class="control is-grouped">
                  <p class="control">
                    <button class="button is-primary">Edit Team</button>
                  </p>

                  <p class="control">
                    <button class="button is-link" @click.prevent="toggle('edit')">Cancel</button>
                  </p>
                </div>
              </form>
            </div>
          </div>
          <button class="modal-close" @click="toggle('edit')"></button>
        </div>

        {{-- DELETE THE TEAM --}}
        <div class="modal" v-bind:class="{
          'is-active': modals.delete.visible
        }"
        >
          <div class="modal-background" @click="toggle('delete')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Delete Team</h1>

              <form method="post" action="{{ route('helpdesk.admin.teams.destroy', $team->id) }}">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}

                <p class="control">
                  <label class="checkbox">
                    <input type="checkbox" class="checkbox" name="delete_confirmed" value="1">
                    Do you really want to delete this team?
                  </label>
                </p>

                <div class="control is-grouped">
                  <p class="control">
                    <button class="button is-primary">Delete Team</button>
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
    </div>
  </section>

  @if(config('app.debug'))
    <script src="https://unpkg.com/vue/dist/vue.js"></script>
  @else
    <script src="https://unpkg.com/vue/dist/vue.min.js"></script>
  @endif

  <script>
    var app = new Vue({
      el: '.modal-app',
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
@endsection