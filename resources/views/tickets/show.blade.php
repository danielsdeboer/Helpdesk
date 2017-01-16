@extends('helpdesk::layout.main')

@section('content')
  @include('helpdesk::partials.errors')

  <div class="section">
    <div class="container">
      <h1 class="title">Timeline</h1>
      <h2 class="subtitle">For Ticket # {{ $ticket->id }}</h2>

      @if($ticket->content)
        @include($ticket->content->partial())
      @endif

      <a href="{{ route('helpdesk.tickets.public', $ticket->uuid) }}">Permalink</a>
    </div>
  </div>

  @include('helpdesk::tickets.show.toolbar', [
    'withClass' => 'is-collapsed-top'
  ])

  <hr class="is-collapsed-top">

  @foreach($ticket->actions as $action)
    @if ($action->object->is_visible == 1 || ($showPrivate == true && $action->object->is_visible == 0))
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