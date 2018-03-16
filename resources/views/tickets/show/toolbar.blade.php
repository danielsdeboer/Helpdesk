@if (auth()->user())
  <div class="section {{ $withClass or '' }}" id="ticket-toolbar">
    <div class="container">
      @if (hd_is_agent())
        @include('helpdesk::tickets.show.agent-toolbar')
      @endif

      @if (!auth()->guest() && !hd_is_agent())
        @include('helpdesk::tickets.show.user-toolbar')
      @endif
    </div>
  </div>
@endif
