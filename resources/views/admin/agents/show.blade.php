@extends('helpdesk::layout.main')

@section('content')

  @include('helpdesk::partials.errors')

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
              <th></th>
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

                <td>
                  <form class="form" method="POST" action="{{ route('helpdesk.admin.team-members.remove') }}">
                    {{ csrf_field() }}

                    <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                    <input type="hidden" name="team_id" value="{{ $team->id }}">
                    <input type="hidden" name="from" value="agent">
                    <p class="control has-addons has-addons-right">
                      <button class="button">Remove From Team</button>
                    </p>
                  </form>
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
                    <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">{{ $ticket->content->title() }}</a>
                  </td>

                  <td>
                    {{ $ticket->created_at->toDateString() }}
                  </td>
                </tr>
              @endforeach
            @else
              <tr>
                <td colspan="2" class="has-text-centered">
                  <section class="section">
                    <span class="icon is-large">
                      <i class="material-icons is-mi-large">mood</i>
                    </span>
                    <p>Nothing to see here!</p>
                  </section>
                </td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>

      @include('helpdesk::admin.agents.show.footer')
    </div>
  </section>
@endsection
