@if (auth()->user())
  <div class="section {{ $withClass or '' }}" id="ticket-toolbar">
    <div class="container">
      <div class="modal-app nav has-margin-bottom">
        @if (auth()->user()->agent)
          @include('helpdesk::tickets.show.agent-toolbar')
        @else
          @include('helpdesk::tickets.show.user-toolbar')
        @endif
      </div>
    </div>
  </div>
@endif
