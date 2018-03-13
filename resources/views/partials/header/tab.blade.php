<a
  href="{{ route($route) }}"
  @if(request()->is('*' . $name . '*'))
    class="nav-item is-tab is-active"
    id="header-tab-{{ $name }}-active"
  @else
    class="nav-item is-tab"
    id="header-tab-{{ $name }}"
  @endif
  role="tab"
>
  {{ $text ?? ucwords($name) }}
</a>