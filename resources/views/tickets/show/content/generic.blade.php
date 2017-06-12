<div class="content">
  <p>
    <strong>From</strong> {{ $ticket->user->name }}
    <br>
    <strong>Title:</strong> {{ $ticket->content->title }}
  </p>

  <blockquote>
    @para($ticket->content->body)
  </blockquote>
</div>
