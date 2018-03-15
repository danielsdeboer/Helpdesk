@extends('helpdesk::tickets.show.action')

@section('action-content')
  <strong id="action-opened">{{ $action->name }}</strong>

  <br>

  <em>When</em>: {{ $action->created_at->diffForHumans() }}

  <br>

  <em>By</em>: {{ \Aviator\Helpdesk\Helpers\Helpers::actionCreator($action) }}
@overwrite

@section('action-note')
@overwrite
