<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use ZipArchive;

class UpdatejsonController extends Controller
{
    public function index(Request $request){
        return view('updatejson/index');
    }

    public function updateJson($folderName){
        $newName = explode('/', $folderName);
        $directoryPath = storage_path('app\public\uploads\\'.$newName[1]);
        $files = File::allFiles($directoryPath);
        foreach ($files as $fil) {
            $ht=file_get_contents($fil);
            $ht=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $ht);
            $info = json_decode($ht, true);
            $contador = 0;
            $users = Arr::get($info, 'usuarios');
            foreach ($users as $user => $value) {
                if($value['tipoDocumentoIdentificacion'] == ''){
                    $info['usuarios'][$contador]['tipoDocumentoIdentificacion'] = '123';
                }
                if($value['numDocumentoIdentificacion'] == ''){
                    $info['usuarios'][$contador]['numDocumentoIdentificacion'] = '456';
                }
                if($value['tipoUsuario'] == ''){
                    $info['usuarios'][$contador]['tipoUsuario'] = '789';
                }
                if($value['codSexo'] == ''){
                    $info['usuarios'][$contador]['codSexo'] = '0';
                }
                if($value['codPaisResidencia'] == ''){
                    $info['usuarios'][$contador]['codPaisResidencia'] = '987';
                }
                if($value['codMunicipioResidencia'] == ''){
                    $info['usuarios'][$contador]['codMunicipioResidencia'] = '654';
                }
                if($value['codZonaTerritorialResidencia'] == ''){
                    $info['usuarios'][$contador]['codZonaTerritorialResidencia'] = '321';
                }
                if($value['incapacidad'] == ''){
                    $info['usuarios'][$contador]['incapacidad'] = 'CASI';
                }
                if($value['codPaisOrigen'] == ''){
                    $info['usuarios'][$contador]['codPaisOrigen'] = '000';
                }
                $services = Arr::get($value, 'servicios');
                $consults = Arr::get($services, 'consultas');
                if($consults !== null){
                    $consu = 0;
                    foreach ($consults as $consult) {
                        // Cambios en campos
                        $info['usuarios'][$contador]['servicios']['consultas'][$consu]['causaMotivoAtencion'] = "bien";
                        $info['usuarios'][$contador]['servicios']['consultas'][$consu]['finalidadTecnologiaSalud'] = "20";
                        // Validaciones de campos obligatorios vacios
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['consecutivo'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['consecutivo'] = '0000';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['codPrestador'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['codPrestador'] = '1111';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['codConsulta'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['codConsulta'] = '2222';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['modalidadGrupoServicioTecSal'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['modalidadGrupoServicioTecSal'] = '3333';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['grupoServicios'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['grupoServicios'] = '4444';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['codDiagnosticoPrincipal'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['codDiagnosticoPrincipal'] = '7777';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['tipoDiagnosticoPrincipal'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['tipoDiagnosticoPrincipal'] = '8888';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['tipoDocumentoIdentificacion'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['tipoDocumentoIdentificacion'] = 'UUUU';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['numDocumentoIdentificacion'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['numDocumentoIdentificacion'] = '9999';
                        }
                        if($info['usuarios'][$contador]['servicios']['consultas'][$consu]['conceptoRecaudo'] == ''){
                            $info['usuarios'][$contador]['servicios']['consultas'][$consu]['conceptoRecaudo'] = '0077';
                        }
                        $consu++;
                    }
                }
                $urgencias = Arr::get($services, 'urgencias');
                if($urgencias !== null){
                    $urg = 0;
                    foreach ($urgencias as $urgencia) {
                        // Cambios en campos
                        $info['usuarios'][$contador]['servicios']['urgencias'][$urg]['causaMotivoAtencion'] = "01";
                        // Validaciones de campos obligatorios vacios
                        if($info['usuarios'][$contador]['servicios']['urgencias'][$urg]['consecutivo'] == ''){
                            $info['usuarios'][$contador]['servicios']['urgencias'][$urg]['consecutivo'] = 02;
                        }
                        if($info['usuarios'][$contador]['servicios']['urgencias'][$urg]['codPrestador'] == ''){
                            $info['usuarios'][$contador]['servicios']['urgencias'][$urg]['codPrestador'] = '030';
                        }
                        if($info['usuarios'][$contador]['servicios']['urgencias'][$urg]['codDiagnosticoPrincipal'] == ''){
                            $info['usuarios'][$contador]['servicios']['urgencias'][$urg]['codDiagnosticoPrincipal'] = '05';
                        }
                        if($info['usuarios'][$contador]['servicios']['urgencias'][$urg]['codDiagnosticoPrincipalE'] == ''){
                            $info['usuarios'][$contador]['servicios']['urgencias'][$urg]['codDiagnosticoPrincipalE'] = '06';
                        }
                        if($info['usuarios'][$contador]['servicios']['urgencias'][$urg]['condicionDestinoUsuarioEgreso'] == ''){
                            $info['usuarios'][$contador]['servicios']['urgencias'][$urg]['condicionDestinoUsuarioEgreso'] = '9999';
                        }
                        $urg++;
                    }
                }
                $procedimientos = Arr::get($services, 'procedimientos');
                if($procedimientos !== null){
                    $proced = 0;
                    foreach ($procedimientos as $procedimiento) {
                        // Cambios en campos
                        $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['viaIngresoServicioSalud'] = "1";
                        // Validaciones de campos obligatorios vacios
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['consecutivo'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['consecutivo'] = '2';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['codPrestador'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['codPrestador'] = '3';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['codProcedimiento'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['codProcedimiento'] = '4';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['modalidadGrupoServicioTecSal'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['modalidadGrupoServicioTecSal'] = '5';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['grupoServicios'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['grupoServicios'] = '6';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['finalidadTecnologiaSalud'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['finalidadTecnologiaSalud'] = '7';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['tipoDocumentoIdentificacion'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['tipoDocumentoIdentificacion'] = 'kkk';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['numDocumentoIdentificacion'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['numDocumentoIdentificacion'] = '8';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['codDiagnosticoPrincipal'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['codDiagnosticoPrincipal'] = '9';
                        }
                        if($info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['conceptoRecaudo'] == ''){
                            $info['usuarios'][$contador]['servicios']['procedimientos'][$proced]['conceptoRecaudo'] = '0';
                        }
                        $proced++;
                    }
                }
                $hospitalizacion = Arr::get($services, 'hospitalizacion');
                if($hospitalizacion !== null){
                    $hos = 0;
                    foreach ($hospitalizacion as $hosp) {
                        // Cambios en campos
                        $info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['causaMotivoAtencion'] = "01";
                        $info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['viaIngresoServicioSalud'] = "02";
                        // Validaciones de campos obligatorios vacios
                        if($info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['consecutivo'] == ''){
                            $info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['consecutivo'] = '03';
                        }
                        if($info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['codPrestador'] == ''){
                            $info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['codPrestador'] = '04';
                        }
                        if($info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['codDiagnosticoPrincipal'] == ''){
                            $info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['codDiagnosticoPrincipal'] = '05';
                        }
                        if($info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['codDiagnosticoPrincipalE'] == ''){
                            $info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['codDiagnosticoPrincipalE'] = '06';
                        }
                        if($info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['condicionDestinoUsuarioEgreso'] == ''){
                            $info['usuarios'][$contador]['servicios']['hospitalizacion'][$hos]['condicionDestinoUsuarioEgreso'] = '07';
                        }
                        $hos++;
                    }
                }
                $medicamentos = Arr::get($services, 'medicamentos');
                if($medicamentos !== null){
                    $med = 0;
                    foreach ($medicamentos as $medicamento) {
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['consecutivo'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['consecutivo'] = '001';
                        }
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['codPrestador'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['codPrestador'] = '002';
                        }
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['codDiagnosticoPrincipal'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['codDiagnosticoPrincipal'] = '003';
                        }
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['tipoMedicamento'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['tipoMedicamento'] = '004';
                        }
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['codTecnologiaSalud'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['codTecnologiaSalud'] = '005';
                        }
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['nomTecnologiaSalud'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['nomTecnologiaSalud'] = '006';
                        }
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['formaFarmaceutica'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['formaFarmaceutica'] = '007';
                        }
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['tipoDocumentoIdentificacion'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['tipoDocumentoIdentificacion'] = '008';
                        }
                        if($info['usuarios'][$contador]['servicios']['medicamentos'][$med]['numDocumentoIdentificacion'] == ''){
                            $info['usuarios'][$contador]['servicios']['medicamentos'][$med]['numDocumentoIdentificacion'] = '009';
                        }
                        $med++;
                    }
                }
                $otrosServicios = Arr::get($services, 'otrosServicios');
                if($otrosServicios !== null){
                    $otros = 0;
                    foreach ($otrosServicios as $otrosServicio) {
                        if($info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['consecutivo'] == ''){
                            $info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['consecutivo'] = '020';
                        }
                        if($info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['codPrestador'] == ''){
                            $info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['codPrestador'] = '030';
                        }
                        if($info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['tipoOS'] == ''){
                            $info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['tipoOS'] = '040';
                        }
                        if($info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['codTecnologiaSalud'] == ''){
                            $info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['codTecnologiaSalud'] = '050';
                        }
                        if($info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['nomTecnologiaSalud'] == ''){
                            $info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['nomTecnologiaSalud'] = '060';
                        }
                        if($info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['tipoDocumentoIdentificacion'] == ''){
                            $info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['tipoDocumentoIdentificacion'] = '070';
                        }
                        if($info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['numDocumentoIdentificacion'] == ''){
                            $info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['numDocumentoIdentificacion'] = '010';
                        }
                        if($info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['conceptoRecaudo'] == ''){
                            $info['usuarios'][$contador]['servicios']['otrosServicios'][$otros]['conceptoRecaudo'] = '080';
                        }
                        $otros++;
                    }
                }
                $updatedJsonUser = json_encode($info, JSON_PRETTY_PRINT);
                File::put($fil, $updatedJsonUser);
                $contador++;
            }
        }
        $message = 'Archivos actualizados correctamente';
        return view('updatejson/message', compact('message'));
    }

    public function uploadFolder(Request $request){
        $request->validate([
            'folder' => 'required|file|mimes:zip|max:10240', // Max 10MB
        ]);
        
        // Store the ZIP file temporarily
        $zipPath = $request->file('folder')->store('temp', 'local');
        $fullZipPath = storage_path('app/' . $zipPath);
        
        // Extract the ZIP file
        $zip = new ZipArchive;
        if ($zip->open($fullZipPath) === TRUE) {
            // Create a unique folder name
            $folderName = 'uploads/' . uniqid() . '/';
            
            // Extract to storage
            $zip->extractTo(storage_path('app/public/' . $folderName));
            $zip->close();
            
            // Delete the temporary ZIP file
            Storage::delete($zipPath);
            
            $this->updateJson($folderName);
        }else{
            $message = 'Hubo un fallo al extraer el archivo ZIP.';
            return view('updatejson/message', compact('message'));  
        }
        $message = 'Archivos actualizados correctamente';
        return view('updatejson/message', compact('message'));
    }
}