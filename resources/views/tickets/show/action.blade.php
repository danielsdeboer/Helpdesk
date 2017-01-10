<article class="media">
  @include('helpdesk::tickets.show.avatar')

  <div class="media-content">
    <div class="level">
      <div class="level-left">
        <div class="level-item">
          <div class="content">
            @yield('action-content')
          </div>
        </div>
      </div>

      @include('helpdesk::tickets.show.badge')
    </div>

    @yield('action-note')
  </div>
</article>