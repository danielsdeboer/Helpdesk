@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong>{{ $action->name }}</strong>
  <br>
  <em>Pool Name</em>: {{ $action->object->pool->name }}
  <br>
  <em>By</em>: {{ $action->object->agent->name or 'System Process' }}
@overwrite

@section('action-note')
@overwrite
