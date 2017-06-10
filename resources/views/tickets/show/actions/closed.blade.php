@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong id="action-header-{{ $action->id }}">{{ $action->name }}</strong>
  <br>
  <em>When</em>: {{ $action->created_at->diffForHumans() }}
  <br>
  <em name="closed-by">By</em>: {{ $action->object->agent ? $action->object->agent->user->name : $action->object->user->name }}
@overwrite

@section('action-note')
  @if ($action->object->note)
    @include('helpdesk::tickets.show.note', [
      'note' => $action->object->note
    ])
  @endif
@overwrite
