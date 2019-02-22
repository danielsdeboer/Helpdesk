@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong>{{ $action->name }}</strong>
  <br>
  <em>Due</em>: {{ $action->object->due_on->diffForHumans() }}
  <br>
  <em>By</em>: {{ $action->object->agent->name ?? 'System Process' }}
@overwrite

@section('action-note')
@overwrite
