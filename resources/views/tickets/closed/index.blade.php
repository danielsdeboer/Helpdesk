@extends('helpdesk::layout.main')

@section('content')
  <div class="section" id="open">
    <div class="container">
      <h1 class="title">Closed Tickets</h1>

      {{ $closed->links('helpdesk::pagination.bulma') }}

      @if (!auth()->user()->agent)
        @include('helpdesk::tickets.closed.index.table-user', [
          'tickets' => $closed
        ])
      @else
        @include('helpdesk::tickets.closed.index.table-agent', [
          'tickets' => $closed
        ])
      @endif

      {{ $closed->links('helpdesk::pagination.bulma') }}
    </div>
  </div>
@endsection