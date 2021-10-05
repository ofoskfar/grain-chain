<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CargarArchivo extends Controller
{
    public function muestraForm(){
        return view('inicio');
    }

    public function cargarTxt(Request $request){
        if($request->file()){
            $archivo = $request->file('file');

            $rutaDestino = 'storage/uploads';
            $nombreFinal = 'matriz.' . $archivo->getClientOriginalExtension();

            $archivo->move($rutaDestino,$nombreFinal);

            return back()
                ->with('success','El archivo ha sido cargado.');
        }
        else{
            return back()
                ->with('error','Selecciona un archivo.');
        }
    }
}
