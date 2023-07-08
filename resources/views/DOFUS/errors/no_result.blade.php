@extends('layouts.contents.default')
@include('layouts.menus.base')

{? $page_name = 'Aucun Résulat' ?}

@section('content')
    {{ $e->getMessage() }}
@stop
