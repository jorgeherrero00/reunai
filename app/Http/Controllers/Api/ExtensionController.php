<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Meeting;
use App\Jobs\ProcesarReunionSubida;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Http\Controllers\Controller;

class ExtensionController extends Controller
{
    public function uploadFromExtension(Request $request)
    {
        $request->validate([
            'titulo'  => 'nullable|string|max:255',
            'audio'   => 'required|file|mimes:webm,mp3,wav,m4a,mp4,mov,avi|max:102400',
        ]);

        $user = $request->attributes->get('api_user');
        if (!$user) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $archivo = $request->file('audio');

        // Nombre temporal
        $originalPath = $archivo->store('reuniones/tmp', 'public');
        $originalFile = storage_path('app/public/' . $originalPath);

        // Nombre final en mp3
        $filename = uniqid('reunion_') . '.mp3';
        $finalPath = storage_path('app/public/reuniones/' . $filename);

        // Convertir a MP3 con ffmpeg
        $process = new Process([
            'ffmpeg',
            '-i', $originalFile,
            '-vn',              // sin video
            '-ar', '44100',     // frecuencia
            '-ac', '2',         // canales
            '-b:a', '192k',     // bitrate
            $finalPath
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Guardar en BD
        $meeting = Meeting::create([
            'user_id'    => $user->id,
            'titulo'     => $request->input('titulo', 'Reunión desde extensión'),
            'archivo'    => 'reuniones/' . $filename, // ya está en public/reuniones
            'formato_origen' => 'audio',
            'guardar_en_google_sheets' => false,
        ]);

        // Borrar original si quieres ahorrar espacio
        unlink($originalFile);

        ProcesarReunionSubida::dispatch($meeting);

        return response()->json([
            'success'    => true,
            'message'    => 'Reunión subida y convertida a MP3 correctamente',
            'meeting_id' => $meeting->id,
            'file'       => $meeting->archivo
        ]);
    }
}
