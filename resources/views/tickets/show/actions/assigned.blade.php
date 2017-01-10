@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong>{{ $action->name }}</strong>
  <br>
  <em>To</em>: {{ $action->object->assignee->name }}
  <br>
  <em>By</em>: {{ $action->object->created_by or 'System Process' }}
@overwrite

@section('action-note')
@overwrite
