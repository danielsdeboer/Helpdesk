@extends('helpdesk::layout.main')

@section('content')
  <div class="section">
    <div class="container">
      <h1 class="title">Ignored Tickets</h1>

      {{ $ignored->links('helpdesk::pagination.bulma') }}

      @if (isset(auth()->user()->agent->is_super) && auth()->user()->agent->is_super)
        @include('helpdesk::tickets.ignored.index.table-super', [
          'tickets' => $ignored
        ])
      @else
        <p>Nothing to see here.</p>
      @endif

      {{ $ignored->links('helpdesk::pagination.bulma') }}
    </div>
  </div>
@endsection
