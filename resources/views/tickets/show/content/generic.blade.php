<div class="content">
  <p>
    <strong>From</strong> {{ $ticket->user->name }}
    <br>
    <strong>Title:</strong> {{ $ticket->content->title }}
  </p>

  <blockquote>
    {{ $ticket->content->body }}
  </blockquote>
</div>