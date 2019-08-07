@extends('layouts.adminpages')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Users</h1>
    </div>
    <a class="btn btn-sm btn-primary" href="{{route('users.index')}}">Back</a>
    <h2>{{$title}}</h2>
    <form method="post" action="{{ route('users.update', ['id' => $user->id]) }}" data-parsley-validate class="form-horizontal form-label-left">

        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} row">
            <label for="name" class="col-sm-2 col-form-label">Name</label>
            <div class="col-sm-10">
                <input type="text" value="{{$user->name}}" id="name" name="name" class="form-control col-md-7 col-xs-12"> @if ($errors->has('name'))
                <span class="help-block">{{ $errors->first('name') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} row">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="text" value="{{$user->email}}" id="email" name="email" class="form-control col-md-7 col-xs-12"> @if ($errors->has('email'))
                <span class="help-block">{{ $errors->first('email') }}</span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('role_id') ? ' has-error' : '' }} row">
            <label class="col-sm-2 col-form-label" for="category_id">Role
                <span class="required">*</span>
            </label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <select class="form-control" id="role_id" name="role_id">
                    @if(count($roles))
                    @foreach($roles as $row)
                    <option value="{{$row->id}}" {{$row->id == $user->roles[0]->id ? 'selected="selected"' : ''}}>{{$row->name}}</option>
                    @endforeach
                    @endif
                </select>
                @if ($errors->has('role_id'))
                <span class="help-block">{{ $errors->first('role_id') }}</span>
                @endif
            </div>
        </div>

        <div class="ln_solid"></div>

        <div class="form-group">
            <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                <input type="hidden" name="_token" value="{{ Session::token() }}">
                <input name="_method" type="hidden" value="PUT">
                <button type="submit" class="btn btn-success">Save User Changes</button>
            </div>
        </div>
    </form>
</main>
</div>
</div>
@endsection