@extends('helpdesk::layout.main')

@section('content')
  <div class="section">
    <div class="container">
      <h1 class="title">Timeline</h1>
      <h2 class="subtitle">For Ticket # {{ $ticket->id }}</h2>

      <?php $contentName = strtolower(str_replace('Aviator\Helpdesk\Models\\', '', $ticket->content_type)); ?>
      @include('helpdesk::tickets.show.content.' . $contentName)
    </div>
  </div>

  @include('helpdesk::tickets.show.toolbar', [
    'withClass' => 'is-collapsed-top'
  ])

  <hr class="is-collapsed-top">

  @foreach($ticket->actions as $action)
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
  @endforeach
@endsection