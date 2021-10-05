<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalculoBombillos extends Controller
{
    var $total_bombillos;

    public function index(){
        $matriz_original = $this->leerMatriz();
        $diagrama_original = $this->convertirTablaHtml($matriz_original);
        $diagrama_optimizado = $this->convertirTablaHtml($this->distribucionBombillos($matriz_original));
        return view('calculo')
            ->with('matriz_original', $diagrama_original)
            ->with('matriz_optimizada', $diagrama_optimizado)
            ->with('total', $this->total_bombillos);
        #echo json_encode($this->distribucionBombillos($matriz_original));
    }

    private function leerMatriz(){
        $matriz_original = [];
        $arch_matriz = fopen("storage/uploads/matriz.txt", "r");
        while(!feof($arch_matriz)){
            $renglon = fgets($arch_matriz);
            $renglon = preg_replace("/\s\s+/", "", $renglon);
            $renglon = preg_replace("/\r?\n|\r/", "", $renglon);
            if(strlen($renglon) > 0){
                $matriz_original[] = str_split($renglon);
            }
        }
        fclose($arch_matriz);

        return $matriz_original;
    }

    private function convertirTablaHtml($matriz){
        $colores_fondo = ['gray', 'black', 'yellow', 'gray'];
        $resultado = '<table class="tabla-matriz">';
        foreach($matriz as $row){
            $resultado .= '<tr>';
            foreach($row as $col){
                $resultado .= '<td style="background-color: ' . $colores_fondo[$col] . ';"></td>';
            }
            $resultado .= '</tr>';
        }
        $resultado .= '</table>';

        return $resultado;
    }

    private function distribucionBombillos($matriz_original){
        #Se proponen 4 estados por item, 0 - no leido, 1 - pared, 2 - bombillo, 3 - iluminado
        $arr_tot = [];
        $matriz_optimizada = $matriz_original;
        $index_y = 0;
        foreach($matriz_original as $row){
            $index_x = 0;
            foreach($row as $col){
                if($col == 0 && $matriz_optimizada[$index_y][$index_x] == 0){
                    $matriz_estructurada = $this->mapeoKey($matriz_optimizada, [$index_y,$index_x]);
                    $matriz_resultante = $this->conteoIluminacion($matriz_optimizada, $matriz_estructurada);
                    $arr_totales = [];
                    foreach($matriz_resultante as $origen => $alcance){
                        $arr_totales[$origen] = count($alcance);
                    }
                    arsort($arr_totales);
                    $colocacion = explode('-', array_key_first($arr_totales));
                    $matriz_optimizada[$colocacion[0]][$colocacion[1]] = "2";
                    array_push($arr_tot, $colocacion[0] . '-' . $colocacion[1]);
                    $matriz_resultante = $matriz_resultante[array_key_first($arr_totales)];
                    foreach($matriz_resultante as $alcance){
                        $coordenada = explode('-', $alcance);
                        $matriz_optimizada[$coordenada[0]][$coordenada[1]] = "3";
                    }
                }

                $index_x++;
            }
            $index_y++;
        }

        $this->total_bombillos = count($arr_tot);
        return $matriz_optimizada;
    }

    #mapeamos y buscamos coordenadas estrategicas
    private function mapeoKey($matriz_optimizada, $origen){
        $resultado = [
            $origen[0] . '-' . $origen[1] => []
        ];

        foreach($resultado as $coords => $alcance){
            $coordenada = explode('-', $coords);

            #mapeo arriba
            for($w = ($coordenada[0] - 1); $w < $coordenada[0]; $w--){
                if(isset($matriz_optimizada[$w][$coordenada[1]])){
                    if($matriz_optimizada[$w][$coordenada[1]] == 0){
                        if(!isset($resultado[$w . '-' . $coordenada[1]])){
                            $resultado[$w . '-' . $coordenada[1]] = [];
                        }
                    }
                    else{
                        $w = count($matriz_optimizada);
                    }
                }
                else{
                    $w = count($matriz_optimizada);
                }
            }

            #mapeo derecha
            for($x = ($coordenada[1] + 1); $x > $coordenada[1]; $x++){
                if(isset($matriz_optimizada[$coordenada[0]][$x])){
                    if($matriz_optimizada[$coordenada[0]][$x] == 0){
                        if(!isset($resultado[$coordenada[0] . '-' . $x])){
                            $resultado[$coordenada[0] . '-' . $x] = [];
                        }
                    }
                    else{
                        $x = - count($matriz_optimizada);
                    }
                }
                else{
                    $x = - count($matriz_optimizada);
                }
            }

            #mapeo abajo
            for($y = ($coordenada[0] + 1); $y > $coordenada[0]; $y++){
                if(isset($matriz_optimizada[$y][$coordenada[1]])){
                    if($matriz_optimizada[$y][$coordenada[1]] == 0){
                        if(!isset($resultado[$y . '-' . $coordenada[1]])){
                            $resultado[$y . '-' . $coordenada[1]] = [];
                        }
                    }
                    else{
                        $y = - count($matriz_optimizada);
                    }
                }
                else{
                    $y = - count($matriz_optimizada);
                }
            }

            #mapeo izquierda
            for($z = ($coordenada[1] - 1); $z < $coordenada[1]; $z--){
                if(isset($matriz_optimizada[$coordenada[0]][$z])){
                    if($matriz_optimizada[$coordenada[0]][$z] == 0){
                        if(!isset($resultado[$coordenada[0] . '-' . $z])){
                            $resultado[$coordenada[0] . '-' . $z] = [];
                        }
                    }
                    else{
                        $z = count($matriz_optimizada);
                    }
                }
                else{
                    $z = count($matriz_optimizada);
                }
            }

        } 

        return $resultado;
    }

    #conteo para verificar cual es la mejor opcion para agregar bombillo
    private function conteoIluminacion($matriz_optimizada, $estructura){
        $resultado = $estructura;

        foreach($resultado as $coords => $alcance){
            $coordenada = explode('-', $coords);

            #mapero arriba
            for($w = ($coordenada[0] - 1); $w < $coordenada[0]; $w--){
                if(isset($matriz_optimizada[$w][$coordenada[1]])){
                    if($matriz_optimizada[$w][$coordenada[1]] == 0){
                        $resultado[$coords][] = $w . '-' . $coordenada[1];
                    }
                    else{
                        $w = count($matriz_optimizada);
                    }
                }
                else{
                    $w = count($matriz_optimizada);
                }
            }

            #mapero derecha
            for($x = ($coordenada[1] + 1); $x > $coordenada[1]; $x++){
                if(isset($matriz_optimizada[$coordenada[0]][$x])){
                    if($matriz_optimizada[$coordenada[0]][$x] == 0){
                        $resultado[$coords][] = $coordenada[0] . '-' . $x;
                    }
                    else{
                        $x = - count($matriz_optimizada);
                    }
                }
                else{
                    $x = - count($matriz_optimizada);
                }
            }

            #mapero abajo
            for($y = ($coordenada[0] + 1); $y > $coordenada[0]; $y++){
                if(isset($matriz_optimizada[$y][$coordenada[1]])){
                    if($matriz_optimizada[$y][$coordenada[1]] == 0){
                        $resultado[$coords][] = $y . '-' . $coordenada[1];
                    }
                    else{
                        $y = - count($matriz_optimizada);
                    }
                }
                else{
                    $y = - count($matriz_optimizada);
                }
            }

            #mapero izquierda
            for($z = ($coordenada[1] - 1); $z < $coordenada[1]; $z--){
                if(isset($matriz_optimizada[$coordenada[0]][$z])){
                    if($matriz_optimizada[$coordenada[0]][$z] == 0){
                        $resultado[$coords][] = $coordenada[0] . '-' . $z;
                    }
                    else{
                        $z = count($matriz_optimizada);
                    }
                }
                else{
                    $z = count($matriz_optimizada);
                }
            }

        } 

        return $resultado;
    }
}
