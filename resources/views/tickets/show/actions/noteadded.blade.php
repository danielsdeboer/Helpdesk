@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong>{{ $action->name }}</strong>
  <br>
  <em>By</em>: {{ $action->object->creator->name }}
@overwrite

@section('action-note')
  @include('tickets.internal.show.note', [
    'note' => $action->object->body
  ])
@overwrite