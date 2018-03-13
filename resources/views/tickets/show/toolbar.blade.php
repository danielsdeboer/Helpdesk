@if (auth()->user())
  <div class="section {{ $withClass or '' }}" id="ticket-toolbar">
    <div class="container">
      @if (auth()->user()->agent)
        @include('helpdesk::tickets.show.agent-toolbar')
      @else
        @include('helpdesk::tickets.show.user-toolbar')
      @endif
    </div>
  </div>
@endif
