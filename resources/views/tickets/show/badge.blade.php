<div class="level-right">
  <div class="level-item">
    @if ($action->object->is_visible)
      <span class="tag is-medium is-warning">
        Public
      </span>
    @else
      <span class="tag is-medium is-success">
        Private
      </span>
    @endif
  </div>
</div>