<?php

namespace App\Http\Controllers\Ventas;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Services\Ventas\FacturacionService;
use App\Services\Ventas\FacturanteService;

class FacturanteController extends Controller
{
	private $facturacionService;
    private $facturanteService;

    public function __construct(FacturacionService $facturacionservice,
                                FacturanteService $facturanteservice)
    {
        $this->facturacionService = $facturacionservice;
        $this->facturanteService = $facturanteservice;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crearImportacion()
    {
        return view('ventas.facturante.crear');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listarComprobanteFull(Request $request)
    {
        $response = array();
        $parameters = $request->all();

        $rules =  array(
              'desdefecha'    => 'required',
              'hastafecha'    => 'required'
          );
          
        $messages = array(
              'desdefecha.required' => 'Fecha desde es requerida.',
              'hastafecha.required' => 'Fecha hasta es requerida.',
          );
        
        $desdefecha = $parameters['desdefecha'];
        $hastafecha = $parameters['hastafecha'];
        
        $validator = \Validator::make(array('desdefecha' => $desdefecha,
                                            'hastafecha' => $hastafecha), $rules, $messages);
        if(!$validator->fails()) {
              $retorno = $this->facturanteService->listadoComprobanteFull($parameters);

              if (is_array($retorno))
                $datas = $retorno;
              else
              {
                // Convierte en coleccion al ser 1 solo item
                $datas = json_encode($retorno);

                $d = json_decode(stripslashes($datas));

                $data = collect([$d]);
                $datas = $data;
              }

              return view('ventas.facturante.index', compact('datas', 'desdefecha', 'hastafecha'));
           } else {
              $errors = $validator->errors();
              return response()->json($errors->all());
           }

        return redirect('ventas/crearimportacionfacturastiendanube')->with('mensaje', 'Comprobantes leidos con éxito');
    }

    public function generarFacturasTiendaNube($desdefecha, $hastafecha)
    {
        $parameters = ['desdefecha' => $desdefecha, 'hastafecha' => $hastafecha];
        $datas = $this->facturanteService->listadoComprobanteFull($parameters);

        if (is_array($datas))
        {
            foreach($datas as $comprobante)
            {
                if (is_array($comprobante))
                    $this->facturanteService->generaFactura($comprobante);
            }
        }
        else
        {
            // Convierte en coleccion al ser 1 solo item
            $datas = json_encode($datas);

            $d = json_decode(stripslashes($datas));

            $data = collect([$d]);
            $datas = $data;           

            $this->facturanteService->generaFactura($datas[0]);
        }

        return redirect('ventas/crearimportacionfacturastiendanube')->with('mensaje', 'Comprobantes grabados con éxito');
    }
}
