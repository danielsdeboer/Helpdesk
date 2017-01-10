@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong>{{ $action->name }}</strong>
  <br>
  <em>When</em>: {{ $action->created_at->diffForHumans() }}
  <br>
  <em>By</em>: {{ $action->object->agent ? $action->object->agent->name : $action->object->user->name }}
@overwrite

@section('action-note')
@overwrite