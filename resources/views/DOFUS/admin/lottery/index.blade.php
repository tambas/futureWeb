@extends('layouts.admin.admin')
@section('title') Loteries @endsection
@section('page-title') Loteries: Manage @endsection
@section('content')
        <!-- Start content -->
<div class="content">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <a href="{{ route('admin.lottery.create') }}" class="btn btn-primary waves-effect waves-light btn-lg m-b-5"><i class="zmdi zmdi-plus"></i> Create Lottery</a>
                    <h4 class="header-title m-b-30">Loteries</h4>
                    <table class="table table-striped" id="tickets-table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Icon</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($lotteryTypes as $type)
                            <tr>
                                <td>{{$type->name}}</td>
                                <td> <img width="70" src="{{ URL::asset($type->icon_path) }}" class="img-maxresponsive"></td>
                                <td> <img width="70" src="{{ URL::asset($type->image_path) }}" class="img-maxresponsive"></td>
                                <td>
                                    <a href="{{route('admin.lottery.edit', $type->id)}}" class="edit btn btn-xs btn-default" data-toggle="tooltip" title="Edit"><i class="fa fa-pencil"></i></a>
                                    <a href="{{route('admin.lottery.items', $type->id)}}" class="edit btn btn-xs btn-default" data-toggle="tooltip" title="Items"><i class="fa fa-gift"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
    @endsection
