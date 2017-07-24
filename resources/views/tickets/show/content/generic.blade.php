<div class="content">
  <p>
    <strong>From</strong> {{ $ticket->user->name or '(deleted)' }}
    <br>
    <strong>Title:</strong> {{ $ticket->content->title }}
  </p>

  <blockquote>
    @para($ticket->content->body)
  </blockquote>
</div>
