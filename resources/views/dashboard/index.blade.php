@extends('layouts.default')

@section('content')
    <h1>Shop installed</h1>

    <div id="hello-react"></div>
@endsection

@section('scripts')
    @parent

    <script>
        actions.TitleBar.create(app, { title: 'Welcome to site' });
    </script>
@endsection