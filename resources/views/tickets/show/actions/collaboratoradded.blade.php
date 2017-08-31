@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong id="action-header-{{ $action->id }}">{{ $action->name }}</strong>
  <br>
  <em>To</em>: {{ $action->object->agent->user->name }}
  <br>
  <em>By</em>: {{ $action->object->created_by or 'System Process' }}
@overwrite

@section('action-note')
@overwrite
