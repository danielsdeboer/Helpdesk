@extends('helpdesk::layout.main')

@section('content')

  @include('helpdesk::admin.tabs', [
    'adminTab' => 'agents'
  ])

  <section class="section">
    <div class="card">
      <div class="card-content">
        <div class="level">
          <div class="level-left">
            <div>
              <h1 class="title"><strong>{{ $agent->user->name }}</strong></h1>
              <h2 class="subtitle">Added on {{ $agent->created_at->toDateString() }}</h2>
            </div>
          </div>

          <div class="level-right">
            <div class="columns">
              <div class="column">
                <span class="tag is-info is-medium">In {{ $agent->teams->count() }} teams</span>
              </div>

              <div class="column">
                @if ($tickets->count() == 0)
                  <span class="tag is-success is-medium">{{ $tickets->count() }} open tickets</span>
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
              <th>Team Name</th>
              <th>Added</th>
            </tr>
          </thead>

          <tbody>
            @foreach($agent->teams as $team)
              <tr>
                <td>
                  <a href="{{ route('helpdesk.admin.teams.show', $team->id) }}">{{ $team->name }}</a>
                </td>

                <td>
                  {{ $team->pivot->created_at->toDateString() }}
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
            @if ($tickets->count() > 0)
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
            @else
              <tr>
                <td colspan="2" class="has-text-centered">
                  <div>
                    <span class="icon is-large">
                      <i class="material-icons is-mi-large">mood</i>
                    </span>
                    <p>Nothing to see here!</p>
                  </div>
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      <footer class="modal-app card-footer">
        <a class="card-footer-item" @click="toggle('add')">
          Add Agent To Team
        </a>

        <a class="card-footer-item" @click="toggle('delete')">
          Delete Agent
        </a>
      </footer>

        {{--
        <div class="modal" v-bind:class="{
          'is-active': modals.add.visible
        }"
        >
          <div class="modal-background" @click="toggle('add')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Add An Agent To This Team</h1>

              <form method="post" action="{{ route('add-agent-to-team', $team->id) }}">
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

        <div class="modal" v-bind:class="{
          'is-active': modals.edit.visible
        }"
        >
          <div class="modal-background" @click="toggle('edit')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Edit Team</h1>

              <form method="post" action="{{ route('edit-team', $team->id) }}">
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

        <div class="modal" v-bind:class="{
          'is-active': modals.delete.visible
        }"
        >
          <div class="modal-background" @click="toggle('delete')"></div>
          <div class="modal-content">
            <div class="box">
              <h1 class="title">Delete Team</h1>

              <form method="post" action="{{ route('delete-team', $team->id) }}">
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

  --}}
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

@endsection
