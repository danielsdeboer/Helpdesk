@if (auth()->user())
  <div class="section {{ $withClass or '' }}" id="ticket-toolbar">
    <div class="container">
      @if (isAgent())
        @include('helpdesk::tickets.show.agent-toolbar')
      @endif

      @if (!auth()->guest() && !isAgent())
        @include('helpdesk::tickets.show.user-toolbar')
      @endif
    </div>
  </div>
@endif
