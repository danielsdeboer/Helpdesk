<div class="nav-item has-text-centered">
  <div id="toolbar-action-{{ $modal }}">
    <p class="heading">{{ $text }}</p>

    <span class="icon">
      <a @click="toggle('{{ $modal }}')">
        <i class="material-icons">{{ $icon }}</i>
      </a>
    </span>
  </div>
</div>
