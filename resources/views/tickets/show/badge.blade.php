<div class="level-right">
  <div class="level-item">
    @if ($action->object->is_visible)
      <span class="tag is-medium is-warning" id="action-{{ $action->id }}-public">
        Public
      </span>
    @else
      <span class="tag is-medium is-success" id="action-{{ $action->id }}-private">
        Private
      </span>
    @endif
  </div>
</div>