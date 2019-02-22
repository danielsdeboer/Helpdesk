@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong id="action-assigned-to-team">Assigned To Team</strong>
  <br>
  <em>Team Name</em>: {{ $action->object->team->name }}
  <br>
  <em>By</em>: {{ $action->object->agent->name ?? 'System Process' }}
@overwrite

@section('action-note')
@overwrite
