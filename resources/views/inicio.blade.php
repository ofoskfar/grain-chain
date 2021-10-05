@extends('layout')

@section('contenido')
    <div class="row justify-content-md-center">
        <div class="col-4 align-self-center">
            <h1>Bienvenido</h1>
            <form action="{{route('cargarTxt')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="txt" class="form-label">Cargar Archivo TXT</label>
                    <input id="txt" name="file" type="file" class="form-control" accept=".txt"></br>
                    <button type="submit" class="btn btn-primary">Cargar</button>
                </div>
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">{{ $message }}</div>
                    <a href="{{route('indexBombillos')}}" class="btn btn-success">Calcular bombillos</a>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger">{{ $message }}</div>
                @endif
            </form>
        </div>
    </div>
@endsection
