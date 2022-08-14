@extends('layout.base')

@section('content')
    <h1>DBF Viewer</h1>
    <form id="form">
        <label for="firstname">
            File
            <input type="text" id="file" name="file" placeholder="file" required>
        </label>
        <button type="submit">View file</button>
    </form>
    <script>
        document.getElementById("form").addEventListener("submit", goToFile);
        const ENC = {
            '+': '-',
            '/': '_',
            '=': '.'
        }
        const DEC = {
            '-': '+',
            _: '/',
            '.': '='
        }

        const encode = (base64) => {
            return base64.replace(/[+/=]/g, (m) => ENC[m])
        }
        const trim = (string) => {
            return string.replace(/[.=]{1,2}$/, '')
        }

        function goToFile(e) {
            e.preventDefault();
            let thing = document.getElementById("file");
            window.location = "/dbf/view/" + trim(encode(btoa(thing.value)));
        }
    </script>
@endsection
