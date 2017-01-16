@extends('helpdesk::layout.main')

@section('content')
  @include('helpdesk::partials.errors')

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
                  <a href="{{ route('helpdesk.tickets.show', $ticket->id) }}">{{ $ticket->content->title() }}</a>
                </td>

                <td>
                  {{ $ticket->created_at->toDateString() }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @include('helpdesk::admin.teams.show.footer')
    </div>
  </section>
@endsection
