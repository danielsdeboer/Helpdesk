@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong>Assigned To Team</strong>
  <br>
  <em>Team Name</em>: {{ $action->object->pool->name }}
  <br>
  <em>By</em>: {{ $action->object->agent->name or 'System Process' }}
@overwrite

@section('action-note')
@overwrite
