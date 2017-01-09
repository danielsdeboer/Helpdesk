<nav class="nav has-shadow">
  <div class="container is-fluid">
    <div class="nav-left">
      <a href="{{ route('helpdesk.dashboard.agent') }}" class="nav-item is-tab @if(isset($tab) && $tab == 'dashboard') is-active @endif">
        Dashboard
      </a>
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