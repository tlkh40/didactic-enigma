@extends('layout.base')

@section('content')
    <div class="navigation">
        <h1>DBF Viewer</h1>
        <div>
            <a href="{{ Helper::url('view.table', ['file' => $file]) }}" role="button">Go back</a>
        </div>
    </div>
    <form id="form" method="POST" action="{{ Helper::url() }}">
        @foreach ($cols as $c)
            <label for="{{ trim($c) }}">
                {{ trim($c) }}
                <input type="text" id="file" name="{{ $c }}" placeholder="{{ $c }}" value="{{ $record->{$c} }}" required>
            </label>
        @endforeach
        <button type="submit">Update</button>
    </form>
@endsection
