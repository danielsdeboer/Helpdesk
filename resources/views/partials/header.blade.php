<nav class="nav has-shadow">
  <div class="container is-fluid">
    <div class="nav-left" role="tablist">
      @include('helpdesk::partials.header.tab', [
        'route' => 'helpdesk.dashboard.router',
        'name' => 'dashboard'
      ])

      @include('helpdesk::partials.header.tab', [
        'route' => 'helpdesk.tickets.index',
        'name' => 'tickets'
      ])

      @if (auth()->user() && auth()->user()->agent && auth()->user()->agent->isSuper())
        @include('helpdesk::partials.header.tab', [
          'route' => 'helpdesk.admin',
          'name' => 'admin'
        ])
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
      @foreach(config('helpdesk.header.links') as $route => $text)
        @include('helpdesk::partials.header.item', compact('route', 'text'))
      @endforeach

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