@extends('helpdesk::layout.main')

@section('content')
  @if (count($errors) > 0)
    <section class="hero is-danger">
      <div class="hero-body">
        <div class="container">
          <h1 class="title">Oops!</h1>
          <h2 class="subtitle">Look like we found some problems:</h2>

          @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        </div>
      </div>
    </section>
  @endif

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
    @if ($action->object->visibility == 1 || ($showPrivate == $true && $action->object->visibility == 0))
      <section class="section is-small">
        <div class="container">
          <?php $actionName = strtolower(str_replace(' ', '', $action->name)); ?>
          @include('helpdesk::tickets.show.actions.' . $actionName)
        </div>
      </section>
    @endif

    @if ($loop->last)
      <hr class="is-collapsed-bottom">
    @else
      <hr>
    @endif
  @endforeach
@endsection