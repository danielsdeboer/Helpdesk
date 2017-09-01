@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong id="action-header-{{ $action->id }}">{{ $action->name }}</strong>
  <br>
  <em>Added</em>: {{ $action->object->agent->user->name }}
  <br>
  <em>By</em>: {{ $action->object->createdBy->user->name or 'System Process' }}
@overwrite

@section('action-note')
@overwrite
