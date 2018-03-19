@extends('helpdesk::layout.main')

@section('content')
  @include('helpdesk::partials.errors')

  <div class="section">
    <div class="container">
      <div class="level">
        <div class="level-left">
          <div>
            <p class="title">{{ $ticket->content->title() }}</p>
            <p class="subtitle">Timeline For Ticket # {{ $ticket->id }}</p>
          </div>
        </div>

        @include('helpdesk::tickets.show.status')
      </div>

      @if($ticket->content)
        @include($ticket->content->partial())
      @endif

      <a href="{{ route('helpdesk.tickets.permalink.show', $ticket->uuid) }}">Permalink</a>
    </div>
  </div>

  @include('helpdesk::tickets.show.toolbar', [
    'withClass' => 'is-collapsed-top'
  ])

  <hr class="is-collapsed-top">

  @foreach($ticket->actions as $action)
    @if (isset(auth()->user()->agent) || $action->object->is_visible == 1)
      <section class="section is-small">
        <div class="container">
          <?php $actionName = strtolower(str_replace(' ', '', $action->name)); ?>
          @include('helpdesk::tickets.show.actions.' . $actionName)
        </div>
      </section>

      @if ($loop->last)
        <hr class="is-collapsed-bottom">
      @else
        <hr>
      @endif
    @endif
  @endforeach
@endsection
