@extends('helpdesk::layout.main')

@section('content')

  @include('helpdesk::partials.errors')

  @include('helpdesk::admin.tabs', [
    'adminTab' => 'disabled'
  ])

  {{-- @include('helpdesk::admin.agents.index.toolbar') --}}

  <section class="section">
    <table class="table">
      <thead>
        <th>Name</th>
        <th>Email</th>
        <th>Teams</th>
      </thead>

      <tbody>
        @foreach($agents as $agent)
          <tr>
            <td><a href="{{ route('helpdesk.admin.agents.show', $agent->id) }}">{{ $agent->user->name }}</a></td>

            <td>{{ $agent->user->$email }}</td>

            <td>
              @foreach($agent->teams as $team)
                @if($loop->last)
                  <a href="{{ route('helpdesk.admin.teams.show', $team->id) }}">{{ $team->name }}</a>
                @else
                  <a href="{{ route('helpdesk.admin.teams.show', $team->id) }}">{{ $team->name }}</a>,&nbsp;
                @endif
              @endforeach
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </section>
@endsection
