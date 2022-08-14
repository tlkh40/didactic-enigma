@extends('layout.base')

@section('content')
    <div class="navigation">
        <h1>DBF Viewer</h1>
        <div>
            <a href="/dbf" role="button">Go back</a>
        </div>
    </div>
    <div class="navigation">
        <h6>
            {{ $page + 1 }} out of {{ $maxPage + 1 }}
        </h6>
        <div>
            <a @if ($page <= 0) disabled @endif role="button"
                href="{{ Pagination::getPrevious($page) }}">Back</a>
            <a @if ($page >= $maxPage) disabled @endif href="{{ Pagination::getNext($page) }}"
                role="button">Next</a>
        </div>
    </div>
    <figure>
        <table>
            <thead>
                <tr>
                    <th>Action</th>
                    @foreach ($cols as $c)
                        <th scope="col">{{ $c }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>

                @foreach ($rows as $r)
                    <tr>
                        <td>
                            <a href="{{ Helper::url('view.record', [
                                'file' => $file,
                                'position' => $r['position'],
                            ]) }}"
                                role="button">Edit</a>
                        </td>
                        @foreach ($cols as $c)
                            <td>
                                {{ $r[trim($c)] }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </figure>
@endsection
