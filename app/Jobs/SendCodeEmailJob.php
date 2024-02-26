<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Mail\SendCodeMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCodeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            //busca el id del user y despues manda el mail al destinatario
            Log::info('Iniciando el envío de correo electrónico.');
            $User = User::findOrFail($this->user->id);
            Mail::to('egmr.49@gmail.com')->send(new SendCodeMail($User));
        } catch (MailQueueingException $e) {
            // maneja la exception del envio del correo
            Log::error('Error al enviar el correo electrónico: ' . $e->getMessage());
        } catch (\Exception $e) {
        // Manejar cualquier otra excepción
        Log::error('Error al enviar el correo electrónico: ' . $e->getMessage());
        }   catch(PDOException $e){
            //Maneja la exception PDO
            Log::error('Error al enviar el correo electronico'. $e->getMessage());
        }
    }
}
