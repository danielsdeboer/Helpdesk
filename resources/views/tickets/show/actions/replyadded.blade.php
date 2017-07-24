@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong id="action-header-{{ $action->id }}">{{ $action->name }}</strong>
  <br>
  <em name="reply-by">By</em>: {{ \Aviator\Helpdesk\Helpers\Helpers::actionCreator($action) }}
@overwrite

@section('action-note')
  @include('helpdesk::tickets.show.note', [
    'note' => $action->object->body
  ])
@overwrite
