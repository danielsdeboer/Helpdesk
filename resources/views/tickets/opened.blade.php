@extends('helpdesk::layout.main')

@section('content')
  <div class="section" id="open">
    <div class="container">
      <h1 class="title">Open Tickets</h1>

      {{ $open->links('helpdesk::pagination.bulma') }}

      <div class="section">
        @include('helpdesk::dashboard.sections.table', [
          'tickets' => $open,
          'withDue' => true,
        ])
      </div>

      {{ $open->links('helpdesk::pagination.bulma') }}
    </div>
  </div>
@endsection