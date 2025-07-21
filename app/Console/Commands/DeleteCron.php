<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Folder;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class DeleteCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete folders after 24 hours and before 36 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = Carbon::now();
        // Restar 1 día
        $oneDayBefore = $date->copy()->subDays(1);
        $now = $oneDayBefore->format('Y-m-d H:i:s');

        // Restar 3 días
        $threeDaysBefore = $date->copy()->subDays(3);
        $newDate = $threeDaysBefore->format('Y-m-d H:i:s');

        $foldersToDelete = Folder::where('discharged', 0)->where('created_at', '>=', $newDate)->where('created_at', '<=', $now)->get();
        foreach($foldersToDelete as $folderToDelete){
            // Eliminar la carpeta creada
            $folderPath = storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', 'uploads', $folderToDelete->name]));
            if (File::exists($folderPath)) {
                File::deleteDirectory($folderPath);
            }

            // Eliminar archivo zip creado
            $zipPath = storage_path(implode(DIRECTORY_SEPARATOR, ['app', 'public', 'uploads', $folderToDelete->name.'.zip']));
            if (File::exists($zipPath)) {
                File::delete($zipPath);
            }

            // Se cambia el valor de la descarga en la base de datos
            $folderDischarged = Folder::select('id')->where('name', $folderToDelete->name)->get();
            foreach($folderDischarged as $fDischarged){
                $folder = Folder::findOrFail($fDischarged->id);
                $folder->discharged = 2;
                $folder->save();
            }
        }
    }
}
