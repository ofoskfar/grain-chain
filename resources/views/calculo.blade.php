@extends('layout')

@section('contenido')
    <div class="row justify-content-md-center">
            <ul class="" style="list-style: none;">
                <li class="" style="background-color: gray; color: white;">Color gris marca espacio libre</li>
                <li class="" style="background-color: black; color: white;">Color negro marca pared</li>
                <li class="" style="background-color: yellow; color: black;">Color amarillo marca bombillo</li>
            </ul>
    </div>
    <div class="row justify-content-md-center">
        <div class="col-6 align-self-center">
            <p>Diagrama Original</p>
            <?php echo $matriz_original; ?>
        </div>
        <div class="col-6 align-self-center">
            <p>Diagrama con Bombillos</p>
            <p>Total Bombillos: <?php echo $total; ?></p>
            <?php echo $matriz_optimizada; ?>
        </div>
    </div>
@endsection