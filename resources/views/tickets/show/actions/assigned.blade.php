@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong id="action-assigned">{{ $action->name }}</strong>
  <br>
  <em>To</em>: {{ $action->object->assignee->user->name }}
  <br>
  <em>By</em>: {{ $action->object->created_by ?? 'System Process' }}
@overwrite

@section('action-note')
@overwrite
