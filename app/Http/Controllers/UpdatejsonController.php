<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Carbon\Carbon;
use ZipArchive;
use Log;

class UpdatejsonController extends Controller
{
    protected $isNotJson = false;
    protected $emptyUserFields = false;
    protected $noUser = false;

    public function index(Request $request){
        $message = '';
        return view('updatejson/index', compact('message'));
    }

    public function uploadFolder(Request $request){
        $request->validate([
            'folder' => 'required|file|mimes:zip|max:30720', // Max 30MB
        ]);
        
        // Guardar el archivo ZIP temporalmente
        $zipPath = $request->file('folder')->store('temp', 'local');
        $fullZipPath = storage_path('app/' . $zipPath);
        $folderName = '';
        
        // Extraer el archivo ZIP
        $zip = new ZipArchive;
        if ($zip->open($fullZipPath) === TRUE) {
            // Crear carpeta con nombre unico
            $folderName = 'uploads/' . uniqid() . '/';
            
            // Extraer en storage
            $zip->extractTo(storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', $folderName])));
            $zip->close();
            
            // Eliminar archivo temporal ZIP
            Storage::delete($zipPath);
            $this->updateJson($folderName);
        }else{
            $message = 'Hubo un fallo al extraer el archivo ZIP.';
            return view('updatejson/message', compact('message'));  
        }
        $folderArray = explode('/', $folderName);
        $folderSend = $folderArray[1];
        if($this->isNotJson == true){
            // Eliminar la carpeta creada
            $folderPath = storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', 'uploads', $folderName]));
            if (File::exists($folderPath)) {
                File::deleteDirectory($folderPath);
            }
            $message = 'Error la extensión de los archivos no es .json';
            $this->isNotJson = false;
            return view('updatejson/index', compact('message'));
        } else if($this->emptyUserFields == true){
            // Eliminar la carpeta creada
            $folderPath = storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', 'uploads', $folderName]));
            if (File::exists($folderPath)) {
                File::deleteDirectory($folderPath);
            }     
            $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
            $this->emptyUserFields = false;
            return view('updatejson/index', compact('message'));
        }else if($this->noUser == true){
            // Eliminar la carpeta creada
            $folderPath = storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', 'uploads', $folderName]));
            if (File::exists($folderPath)) {
                File::deleteDirectory($folderPath);
            }   
            $message = 'La estructura del archivo no contiene información de usuarios, por favor verifiquelos.';
            $this->noUser = false;
            return view('updatejson/index', compact('message'));
        }else{
            $message = 'Archivos actualizados correctamente';
            $this->isNotJson = false;
            return view('updatejson/message', compact('message','folderSend'));
        }
    }

    public function updateJson($folderName){
        $newName = explode('/', $folderName);
        $directoryPath = storage_path(implode(DIRECTORY_SEPARATOR, ['app','public', 'uploads', $newName[1]]));
        $files = File::allFiles($directoryPath);
        foreach ($files as $file) {
            $extension = explode('.', File::basename($file));
            if (strtolower($extension[1]) !== 'json') {
                $this->isNotJson = true;
                $message = 'Error la extensión de los archivos no es .json';
                return $message;
            }
            $content=file_get_contents($file);
            $content=preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $content);
            $information = json_decode($content, true);
            $counter = 0;
            $users = Arr::get($information, 'usuarios');          
            if($users != null){
                foreach ($users as $user => $value) {
                    // Validaciones de campos obligatorios vacios
                    if($value['tipoDocumentoIdentificacion'] == ''){
                        $this->emptyUserFields = true;
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;
                    }
                    if($value['numDocumentoIdentificacion'] == ''){
                        $this->emptyUserFields = true; 
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;     
                    }
                    if($value['tipoUsuario'] == ''){
                        $this->emptyUserFields = true;   
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;            
                    }
                    if($value['codSexo'] == ''){
                        $this->emptyUserFields = true;
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;
                    }
                    if($value['codPaisResidencia'] == ''){
                        $this->emptyUserFields = true;
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;
                    }
                    if($value['codMunicipioResidencia'] == ''){
                        $this->emptyUserFields = true;
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;
                    }
                    if($value['codZonaTerritorialResidencia'] == ''){
                        $this->emptyUserFields = true;
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;
                    }
                    if($value['incapacidad'] == ''){
                        $this->emptyUserFields = true;
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;
                    }
                    if($value['codPaisOrigen'] == ''){
                        $this->emptyUserFields = true;
                        $message = 'Algunos datos obligatorios del usuario están vacios, por favor verifiquelos.';
                        return $message;
                    }
                    $services = Arr::get($value, 'servicios');
                    $consults = Arr::get($services, 'consultas');
                    if($consults !== null){
                        $counterConsult = 0;
                        foreach ($consults as $consult) {
                            // Cambios en campos
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['causaMotivoAtencion'] == '13'
                                || $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['causaMotivoAtencion'] == '')
                            {
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['causaMotivoAtencion'] = '38';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['finalidadTecnologiaSalud'] == '10' 
                                || $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['finalidadTecnologiaSalud'] = '1'
                                || $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['finalidadTecnologiaSalud'] = '2'
                                || $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['finalidadTecnologiaSalud'] = '3'
                                || $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['finalidadTecnologiaSalud'] = '4'
                                || $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['finalidadTecnologiaSalud'] = '5'
                                || $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['finalidadTecnologiaSalud'] = '')
                            {
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['finalidadTecnologiaSalud'] = '15';
                            }
                            // Validaciones de campos obligatorios vacios
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['consecutivo'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['consecutivo'] = 0000;
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['codPrestador'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['codPrestador'] = '1111';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['codConsulta'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['codConsulta'] = '2222';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['modalidadGrupoServicioTecSal'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['modalidadGrupoServicioTecSal'] = '3333';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['grupoServicios'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['grupoServicios'] = '4444';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['codDiagnosticoPrincipal'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['codDiagnosticoPrincipal'] = '7777';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['tipoDiagnosticoPrincipal'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['tipoDiagnosticoPrincipal'] = '8888';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['tipoDocumentoIdentificacion'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['tipoDocumentoIdentificacion'] = 'UUUU';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['numDocumentoIdentificacion'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['numDocumentoIdentificacion'] = '9999';
                            }
                            if($information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['conceptoRecaudo'] == ''){
                                $information['usuarios'][$counter]['servicios']['consultas'][$counterConsult]['conceptoRecaudo'] = '0077';
                            }
                            $counterConsult++;
                        }
                    }
                    $emergencies = Arr::get($services, 'urgencias');
                    if($emergencies !== null){
                        $counterEmergency = 0;
                        foreach ($emergencies as $emergency) {
                            // Cambios en campos
                            if($information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['causaMotivoAtencion'] == '13'
                                || $information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['causaMotivoAtencion'] == '')
                            {
                                $information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['causaMotivoAtencion'] = '38';
                            }
                            // Validaciones de campos obligatorios vacios
                            if($information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['consecutivo'] == ''){
                                $information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['consecutivo'] = 02;
                            }
                            if($information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['codPrestador'] == ''){
                                $information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['codPrestador'] = '030';
                            }
                            if($information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['codDiagnosticoPrincipal'] == ''){
                                $information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['codDiagnosticoPrincipal'] = '05';
                            }
                            if($information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['codDiagnosticoPrincipalE'] == ''){
                                $information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['codDiagnosticoPrincipalE'] = '06';
                            }
                            if($information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['condicionDestinoUsuarioEgreso'] == ''){
                                $information['usuarios'][$counter]['servicios']['urgencias'][$counterEmergency]['condicionDestinoUsuarioEgreso'] = '9999';
                            }
                            $counterEmergency++;
                        }
                    }
                    $procedures = Arr::get($services, 'procedimientos');
                    if($procedures !== null){
                        $counterProcedure = 0;
                        foreach ($procedures as $procedure) {
                            // Cambios en campos
                            $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['viaIngresoServicioSalud'] = '1';
                            // Validaciones de campos obligatorios vacios
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['consecutivo'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['consecutivo'] = 2;
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['codPrestador'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['codPrestador'] = '3';
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['codProcedimiento'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['codProcedimiento'] = '4';
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['modalidadGrupoServicioTecSal'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['modalidadGrupoServicioTecSal'] = '5';
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['grupoServicios'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['grupoServicios'] = '6';
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['finalidadTecnologiaSalud'] == '10' 
                                || $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['finalidadTecnologiaSalud'] = '1'
                                || $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['finalidadTecnologiaSalud'] = '2'
                                || $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['finalidadTecnologiaSalud'] = '3'
                                || $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['finalidadTecnologiaSalud'] = '4'
                                || $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['finalidadTecnologiaSalud'] = '5'
                                || $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['finalidadTecnologiaSalud'] == '')
                            {
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['finalidadTecnologiaSalud'] = '7';
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['tipoDocumentoIdentificacion'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['tipoDocumentoIdentificacion'] = 'kkk';
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['numDocumentoIdentificacion'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['numDocumentoIdentificacion'] = '8';
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['codDiagnosticoPrincipal'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['codDiagnosticoPrincipal'] = '9';
                            }
                            if($information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['conceptoRecaudo'] == ''){
                                $information['usuarios'][$counter]['servicios']['procedimientos'][$counterProcedure]['conceptoRecaudo'] = '0';
                            }
                            $counterProcedure++;
                        }
                    }
                    $hospitalization = Arr::get($services, 'hospitalizacion');
                    if($hospitalization !== null){
                        $counterHospitalization = 0;
                        foreach ($hospitalization as $valueHospitalization) {
                            // Cambios en campos
                            if($information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['causaMotivoAtencion'] == '13'
                                || $information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['causaMotivoAtencion'] == '')
                            {
                                $information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['causaMotivoAtencion'] = '38';
                            }
                            // Validaciones de campos obligatorios vacios
                            if($information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['viaIngresoServicioSalud'] = ''){
                                $information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['viaIngresoServicioSalud'] = '04';
                            }
                            if($information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['consecutivo'] == ''){
                                $information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['consecutivo'] = 03;
                            }
                            if($information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['codPrestador'] == ''){
                                $information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['codPrestador'] = '04';
                            }
                            if($information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['codDiagnosticoPrincipal'] == ''){
                                $information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['codDiagnosticoPrincipal'] = '05';
                            }
                            if($information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['codDiagnosticoPrincipalE'] == ''){
                                $information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['codDiagnosticoPrincipalE'] = '06';
                            }
                            if($information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['condicionDestinoUsuarioEgreso'] == ''){
                                $information['usuarios'][$counter]['servicios']['hospitalizacion'][$counterHospitalization]['condicionDestinoUsuarioEgreso'] = '07';
                            }
                            $counterHospitalization++;
                        }
                    }
                    $medications = Arr::get($services, 'medicamentos');
                    if($medications !== null){
                        $counterMedication = 0;
                        foreach ($medications as $medication) {
                            // Validaciones de campos obligatorios vacios
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['consecutivo'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['consecutivo'] = 001;
                            }
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['codPrestador'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['codPrestador'] = '002';
                            }
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['codDiagnosticoPrincipal'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['codDiagnosticoPrincipal'] = '003';
                            }
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['tipoMedicamento'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['tipoMedicamento'] = '004';
                            }
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['codTecnologiaSalud'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['codTecnologiaSalud'] = '005';
                            }
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['nomTecnologiaSalud'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['nomTecnologiaSalud'] = '006';
                            }
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['formaFarmaceutica'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['formaFarmaceutica'] = '007';
                            }
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['tipoDocumentoIdentificacion'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['tipoDocumentoIdentificacion'] = '008';
                            }
                            if($information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['numDocumentoIdentificacion'] == ''){
                                $information['usuarios'][$counter]['servicios']['medicamentos'][$counterMedication]['numDocumentoIdentificacion'] = '009';
                            }
                            $counterMedication++;
                        }
                    }
                    $otherServices = Arr::get($services, 'otrosServicios');
                    if($otherServices !== null){
                        $counterOtherService = 0;
                        foreach ($otherServices as $otherService) {
                            // Validaciones de campos obligatorios vacios
                            if($information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['consecutivo'] == ''){
                                $information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['consecutivo'] = 020;
                            }
                            if($information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['codPrestador'] == ''){
                                $information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['codPrestador'] = '030';
                            }
                            if($information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['tipoOS'] == ''){
                                $information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['tipoOS'] = '040';
                            }
                            if($information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['codTecnologiaSalud'] == ''){
                                $information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['codTecnologiaSalud'] = '050';
                            }
                            if($information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['nomTecnologiaSalud'] == ''){
                                $information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['nomTecnologiaSalud'] = '060';
                            }
                            if($information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['tipoDocumentoIdentificacion'] == ''){
                                $information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['tipoDocumentoIdentificacion'] = '070';
                            }
                            if($information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['numDocumentoIdentificacion'] == ''){
                                $information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['numDocumentoIdentificacion'] = '010';
                            }
                            if($information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['conceptoRecaudo'] == ''){
                                $information['usuarios'][$counter]['servicios']['otrosServicios'][$counterOtherService]['conceptoRecaudo'] = '080';
                            }
                            $counterOtherService++;
                        }
                    }
                    $updatedJsonUser = json_encode($information, JSON_PRETTY_PRINT);
                    File::put($file, $updatedJsonUser);
                    $counter++;
                }
            } else{
                $this->noUser = true;
                $message = 'La estructura del archivo no contiene información de usuarios, por favor verifiquelos.';
                return $message;
            }
        }
        $this->compressFolder($newName[1]);
        $message = 'Archivos actualizados correctamente';
        return view('updatejson/message', compact('message'));
    }

    public function compressFolder($newName){
        $folderName = $newName;
        //$publicPath = public_path('storage\uploads\\'.$folderName);
        $publicPath = public_path(implode(DIRECTORY_SEPARATOR, ['storage', 'uploads', $folderName]));
        // Revisar si el folder existe
        if (!file_exists($publicPath)) {
            $message = 'La carpeta no se encuentra';
            return view('updatejson/message', compact('message'));
        }
        // Crear un archivo zip temporal
        $zip = new ZipArchive;
        $zipFileName = storage_path(implode(DIRECTORY_SEPARATOR, ['app','public','uploads',$folderName.'.zip']));
        
        if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
            // Agregar todos los archivos desde el folder al zip
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($publicPath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );
            
            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($publicPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();

            // Se agrega nuevo campo en la base de datos
            $folderNew = new Folder;
            $folderNew->name = $folderName;
            $folderNew->save();
        } else {
            $message = 'No puede crear el archivo zip';
            return view('updatejson/message', compact('message'));
        }
    }

    public function downloadFolder(Request $request){
        // Preparando contenido para descargar el archivo zip
        $fileZip = storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', 'uploads', $request->query('namefile').'.zip']));
        $headers = array('Content-Type'=>'application/octet-stream',);
        $zip_new_name = "Archivos actualizados ".date("y-m-d-h-i-s").".zip";

        // Eliminar la carpeta creada
        $folderPath = storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', 'uploads', $request->query('namefile')]));
        if (File::exists($folderPath)) {
            File::deleteDirectory($folderPath);
        }

        // Se cambia el valor de la descarga en la base de datos
        $folderDischarged = Folder::select('id')->where('name', $request->query('namefile'))->get();
        foreach($folderDischarged as $fDischarged){
            $folder = Folder::findOrFail($fDischarged->id);
            $folder->discharged = 1;
            $folder->save();
        }

        // Iniciar la descarga del archivo zip
        return response()->download($fileZip,$zip_new_name,$headers)->deleteFileAfterSend(true);
    }
}