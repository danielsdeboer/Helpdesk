@extends('helpdesk::layout.main')

@section('content')
  <div class="section" id="open">
    <div class="container">
      <h1 class="title">Closed Tickets</h1>

      {{ $closed->links('helpdesk::pagination.bulma') }}

      <div class="section">
        @include('helpdesk::dashboard.sections.table', [
          'tickets' => $closed,
          'withDue' => true,
        ])
      </div>

      {{ $closed->links('helpdesk::pagination.bulma') }}
    </div>
  </div>
@endsection