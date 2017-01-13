@extends('helpdesk::layout.main')

@section('content')
  @include('helpdesk::partials.errors')

  @include('helpdesk::admin.tabs', [
    'adminTab' => 'teams'
  ])

  @include('helpdesk::admin.teams.index.toolbar')

  <section class="section">
    <table class="table">
      <thead>
        <th>Name</th>
        <th>Teams</th>
      </thead>

      <tbody>
        @foreach($teams as $team)
          <tr>
            <td><a href="{{ route('helpdesk.admin.teams.show', $team->id) }}">{{ $team->name }}</a></td>

            <td>
              @foreach($team->agents as $agent)
                @if($loop->last)
                  <a href="{{ route('helpdesk.admin.agents.show', $agent->id) }}">{{ $agent->user->name }}</a>
                @else
                  <a href="{{ route('helpdesk.admin.agents.show', $agent->id) }}">{{ $agent->user->name }}</a>,&nbsp;
                @endif
              @endforeach
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endsection
