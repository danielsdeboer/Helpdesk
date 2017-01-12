<section class="section is-collapsed-bottom">
  <nav class="tabs is-medium is-boxed">
    <ul>
      <li
        @if (isset($adminTab) && $adminTab == 'agents') class="is-active" @endif
        id="tab-admin-agents"
      >
        <a href="{{ route('helpdesk.admin.agents.index') }}">Agents</a>
      </li>

      <li
        @if (isset($adminTab) && $adminTab == 'teams') class="is-active" @endif
        id="tab-admin-teams"
      >
        <a href="{{ route('helpdesk.admin.teams.index') }}">Teams</a>
      </li>
    </ul>
  </nav>
</section>