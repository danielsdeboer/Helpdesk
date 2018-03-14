@extends('helpdesk::layout.main')

@section('content')
  <div class="section">
    <div class="container">
      <h1 class="title">Closed Tickets</h1>

      {{ $open->links('helpdesk::pagination.bulma') }}

      @if (!auth()->user()->agent)
        @include('helpdesk::tickets.open.index.table-user', [
          'tickets' => $open
        ])
      @else
        @include('helpdesk::tickets.open.index.table-agent', [
          'tickets' => $open
        ])
      @endif

      {{ $open->links('helpdesk::pagination.bulma') }}
    </div>
  </div>
@endsection