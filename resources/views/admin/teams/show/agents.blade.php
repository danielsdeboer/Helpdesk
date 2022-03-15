<table class="table" data-section="agents-table">
  <thead>
  <tr>
    <th>Agent Name</th>
    <th>Team Lead</th>
    <th>Added</th>
    <th></th>
  </tr>
  </thead>

  <tbody>
  @foreach($team->agents as $agent)
    <tr>
      <td>
        <a
          data-agent-id="{{ $agent->id }}"
          data-agent-name="{{ $agent->getUserName() }}"
          href="{{ route('helpdesk.admin.agents.show', $agent->id) }}"
        >
          {{ $agent->getUserName() }}
        </a>
      </td>

      <td>
        @if(isset($agent->pivot->is_team_lead))
          {{ $agent->pivot->is_team_lead }}
        @endif
      </td>

      <td>
        {{ $agent->pivot->created_at->toDateString() }}
      </td>


      <td>
        <div class="div-button">
          <form class="form" method="POST" action="{{ route('helpdesk.admin.team-members.make-team-lead') }}">
            {{ csrf_field() }}

            <input type="hidden" name="agent_id" value="{{ $agent->id }}">
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="hidden" name="from" value="agent">
            <button class="button button--margin">Make Team Lead</button>
          </form>

          <form class="form" method="POST" action="{{ route('helpdesk.admin.team-members.remove') }}">
            {{ csrf_field() }}

            <input type="hidden" name="agent_id" value="{{ $agent->id }}">
            <input type="hidden" name="team_id" value="{{ $team->id }}">
            <input type="hidden" name="from" value="agent">
            <button class="button">Remove From Team</button>
          </form>
        </div>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
