@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong id="action-header-{{ $action->id }}">{{ $action->name }}</strong>
  <br>
  <em>By</em>: {{ $action->object->agent ? $action->object->agent->user->name : $action->object->user->name }}
@overwrite

@section('action-note')
  @include('helpdesk::tickets.show.note', [
    'note' => $action->object->body
  ])
@overwrite