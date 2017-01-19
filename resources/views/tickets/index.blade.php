@extends('helpdesk::layout.main')

@section('content')
  <section class="hero is-primary">
    <div class="hero-body">
      <div class="level is-mobile">
        <div class="level-item has-text-centered">
          <a href="#open">
            <div>
              <p class="heading">Open Tickets</p>
              <p class="title"><strong>{{ $openCount }}</strong></p>
            </div>
          </a>
        </div>

        <div class="level-item has-text-centered">
          <a href="#closed">
            <div>
              <p class="heading">Closed Tickets</p>
              <p class="title"><strong>{{ $closedCount }}</strong></p>
            </div>
          </a>
        </div>
      </div>
    </div>
  </section>


  <div class="section" id="open">
    <div class="container">
      <h1 class="title">Open</h1>

      @include('helpdesk::dashboard.sections.table', [
        'tickets' => $open,
        'withDue' => true,
      ])

      @if ($open->hasMorePages())
        <a id="open-see-more" class="button" href="{{ route('helpdesk.tickets.opened') }}">See {{ $closedCount - 25 }} more...</a>
      @else
        <a id="open-see-more" class="button is-disabled">See 0 more...</a>
      @endif
    </div>
  </div>

  <div class="section" id="closed">
    <div class="container">
      <h1 class="title">Closed</h1>

      @include('helpdesk::dashboard.sections.table', [
        'tickets' => $closed,
        'withLastAction' => true
      ])

      @if ($closed->hasMorePages())
        <a id="closed-see-more" class="button" href="{{ route('helpdesk.tickets.closed') }}">See {{ $closedCount - 25 }} more...</a>
      @else
        <a id="closed-see-more" class="button is-disabled">See 0 more...</a>
      @endif
    </div>
  </div>
@endsection