<figure class="media-left">
  <p class="image is-64x64">
    @if (! $action->object->agent_id && ! $action->object->user_id)
      <img src="/vendor/aviator/system-process.jpg">
    @else
      <img src="/vendor/aviator/no-image.png">
    @endif
  </p>
</figure>