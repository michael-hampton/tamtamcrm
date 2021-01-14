@extends('layouts.main')
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">Enter Token</div>
        <div class="card-body">
          @if(\Illuminate\Support\Facades\Session::has('message'))
            <p class="alert alert-danger">{{ \Illuminate\Support\Facades\Session::get('message') }}</p>
          @endif

          <form action="" method="post">
            @csrf
            <div class="form-group">
              <label for="token">Token</label>
              <input type="text" name="token" placeholder="Enter received token" class="form-control{{ $errors->has('token') ? ' is-invalid' : '' }}"
                id="token"> @if ($errors->has('token'))
              <span class="invalid-feedback" role="alert">
                  <strong>{{ $errors->first('token') }}</strong>
                </span> @endif
            </div>

            <button class="btn btn-primary btn-large">Verify</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
