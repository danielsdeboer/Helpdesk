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

        @include('helpdesk::admin.teams.show.agents')
        @include('helpdesk::admin.teams.show.tickets')


      @include('helpdesk::admin.teams.show.footer')
    </div>
  </section>
@endsection
