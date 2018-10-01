@extends('helpdesk::layout.main')

@section('content')
  <div class="section">
    <div class="container">
      <h1 class="title">Ignored Tickets</h1>

      {{ $ignored->links('helpdesk::pagination.bulma') }}

      @include('helpdesk::tickets.ignored.index.table-super', [
        'tickets' => $ignored
      ])

      {{ $ignored->links('helpdesk::pagination.bulma') }}
    </div>
  </div>
@endsection
