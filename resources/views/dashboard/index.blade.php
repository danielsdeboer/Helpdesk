@extends('helpdesk::layout.main')

@section('content')
  <section class="hero is-primary">
    <div class="hero-body">
      <div class="level is-mobile">

        @if (isset($unassigned))
          <div class="level-item has-text-centered">
            <a href="#unassigned">
              <div>
                <p class="heading">Unassigned</p>
                <p class="title"><strong>{{ $unassigned->count() }}</strong></p>
              </div>
            </a>
          </div>
        @endif

        @if (isset($team))
          <div class="level-item has-text-centered">
            <a href="#team">
              <div>
                <p class="heading">Assigned To Team</p>
                <p class="title"><strong>{{ $team->count() }}</strong></p>
              </div>
            </a>
          </div>
        @endif

        @if (isset($overdue))
          <div class="level-item has-text-centered">
            <a href="#overdue">
              <div>
                <p class="heading">Overdue</p>
                <p class="title"><strong>{{ $overdue->count() }}</strong></p>
              </div>
            </a>
          </div>
        @endif

        @if (isset($open))
          <div class="level-item has-text-centered">
            <a href="#open">
              <div>
                <p class="heading">Open</p>
                <p class="title"><strong>{{ $open->count() }}</strong></p>
              </div>
            </a>
          </div>
        @endif

        @if (isset($collab))
          <div class="level-item has-text-centered">
            <a href="#collab">
              <div>
                <p class="heading" id="collab-count-title">Collaborating On</p>
                <p class="title" id="collab-count-number"><strong>{{ $collab->count() }}</strong></p>
              </div>
            </a>
          </div>
        @endif
      </div>
    </div>
  </section>

  @if (isset($unassigned))
    <section class="section" id="unassigned">
      <div class="container">
        <h1 class="title">Unassigned</h1>

        @if($unassigned->count() > 0)
          @include('helpdesk::dashboard.sections.table', [
            'tickets' => $unassigned,
            'withOpened' => true,
            'withLastAction' => true,
          ])
        @else
          @include('helpdesk::dashboard.sections.noresults')
        @endif
      </div>
    </section>

    <hr class="is-collapsed">
  @endif

  @if (isset($team))
    <section class="section" id="team">
      <div class="container">
        <h1 class="title">Assigned To Team</h1>

        @if($team->count() > 0)
          @include('helpdesk::dashboard.sections.table', [
            'tickets' => $team,
            'withOpened' => true,
            'withLastAction' => true,
          ])
        @else
          @include('helpdesk::dashboard.sections.noresults')
        @endif
      </div>
    </section>

    <hr class="is-collapsed">
  @endif

  @if (isset($overdue))
    <section class="section" id="overdue">
      <div class="container">
        <h1 class="title">Overdue</h1>

        @if($overdue->count() > 0)
          @include('helpdesk::dashboard.sections.table', [
            'tickets' => $overdue,
            'withOpened' => true,
            'withLastAction' => true,
          ])
        @else
          @include('helpdesk::dashboard.sections.noresults')
        @endif
      </div>
    </section>

    <hr class="is-collapsed">
  @endif

  @if (isset($open))
    <section class="section" id="open">
      <div class="container">
        <h1 class="title">Open</h1>

        @if($open->count() > 0)
          @include('helpdesk::dashboard.sections.table', [
            'tickets' => $open,
            'withOpened' => true,
            'withLastAction' => true,
          ])
        @else
          @include('helpdesk::dashboard.sections.noresults')
        @endif
      </div>
    </section>
  @endif

  @if (isset($collab))
    <section class="section" id="collab">
      <div class="container">
        <h1 class="title" id="collab-title">Collaborating On</h1>

        @if($collab->count() > 0)
          @include('helpdesk::dashboard.sections.table', [
            'tickets' => $collab,
            'withOpened' => true,
            'withLastAction' => true,
          ])
        @else
          @include('helpdesk::dashboard.sections.noresults', [
            'id' => 'collab-no-results'
          ])
        @endif
      </div>
    </section>
  @endif
@endsection
