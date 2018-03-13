<nav class="nav has-shadow">
  <div class="container is-fluid">
    <div class="nav-left" role="tablist">
      <a
        href="{{ route('helpdesk.dashboard.router') }}"
        @if(request()->is('*dashboard*'))
          class="nav-item is-tab is-active"
          id="header-tab-dashboard-active"
        @else
          class="nav-item is-tab"
          id="header-tab-dashboard"
        @endif
        role="tab"
      >
        Dashboard
      </a>

      <a
        href="{{ route('helpdesk.tickets.index') }}"
        @if(request()->is('*tickets*'))
          class="nav-item is-tab is-active"
          id="header-tab-tickets-active"
        @else
          class="nav-item is-tab"
          id="header-tab-tickets"
        @endif
        role="tab"
      >
        Tickets
      </a>

      @if (auth()->user()->agent && auth()->user()->agent->isSuper())
        <a
          href="{{ route('helpdesk.admin') }}"
          @if(request()->is('*admin*'))
            class="nav-item is-tab is-active"
            id="header-tab-admin-active"
          @else
            class="nav-item is-tab"
            id="header-tab-admin"
          @endif
          role="tab"
        >
          Admin
        </a>
      @endif
    </div>

    <div class="nav-center">
      <a class="nav-item">
        <span class="icon">
          <i class="material-icons">chat</i>
        </span>
      </a>

      <div class="nav-item">
        <h1 class="title"><strong>Helpdesk</strong></h1>
      </div>
    </div>

    <div class="nav-right">
      <div class="nav-item">
        <span class="icon">
          <a href="/">
            <i class="material-icons">home</i>
          </a>
        </span>
      </div>
    </div>
  </div>
</nav>