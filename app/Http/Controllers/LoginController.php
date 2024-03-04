<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException as ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Jobs\SendCodeEmailJob;
use App\Jobs\SendUrlEmailJob;
use App\Mail\SendUrlMail;
use App\Mail\SendCodeMail;
use App\Models\User;

class LoginController extends Controller
{
    public function showLogin(){
        return view('login');
    }

    public function showVerifyCode(Int $userId){
        $url = URL::temporarySignedRoute('validationCode', now()->addMinutes(30), ['user_id' => $userId]);
        return view('verifyCode', ['user_id'=>$userId, 'signedUrl'=>$url]);
    }

    public function showEmailVerify(Int $userId){
        $url = URL::temporarySignedRoute('validationMail', now()->addMinutes(30), ['user_id' => $userId]);
        return view ('emailVerify', ['signedUrl'=> $url]);
    }

    public function showRegister(){
        return view('register');
    }

    public function showHomeAdmin(){
        return view('homeAdmin');
    }

    public function showHomeUser(){
        return view('homeUser');
    }

    public function showMailView(){
        return view('mailView');
    }

    public function loginUser(Request $request){
        //Valida la entrada de los datos email y password
        try {
            $request->validate([
                'email' => 'required|email|max:60',
                'password' => ['required','min:8'],
            ]);

            //Intenta verificar las credenciales
            $credentials = $request->only('email', 'password');

            $user = User::where('email', $credentials['email'])->first();

            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                throw new ModelNotFoundException('Credentials are incorrect');
            }

            $user = User::where('email', $request->email)->first();
            $code = rand(1000,9999);
            Log::info('User: ' . $user->name . ' code: ' . $code);
            $user->code = Hash::make($code);
            $user->save();

            //verifica si el usuario es activo
            if($user->active == false){

                //si no esta activo, le manda el correo para que lo active
                Log::info('Intento de logeo de: ' . $user->name.' no activo');
                SendUrlEmailJob::dispatch($user)->onQueue('emailsUrl');
                return redirect('/verify/code/'.$user->id);
            }

            if($user->rol_id != 3){

                //si es otro usuario diferente al administrador, lo manda a la vista correspondiente
                Auth::login($user);
                Log::info('User logged: ' . $user->name);
                return redirect()->intended('/home/user');
            }

            //si es administrador tiene manda el codigo para el factor de autentificacion
            Log::info('Intento de logeo de: ' . $user->name .' mandando sms');
            Mail::to('egmr.49@gmail.com')->send(new SendCodeMail($user, $code));
            //SendCodeEmailJob::dispatch($user)->onQueue('emailsCode');
            return redirect('/verify/code/'.$user->id);

            }   catch (ModelNotFoundException $exception) {
                //si no encuentra el modelo lo regresa a la vista con un error
                Log::error('ModelNotFoundException: ' . $exception->getMessage());
                return Redirect::back()->withErrors(['errors'=>'Credentials are incorrect']);
            }  catch (\PDOException $exception) {
                // Manejar excepciones de PDO
                Log::error('PDOException: ' . $exception->getMessage());
                return redirect()->back()->withErrors(['errors' => 'An unexpected error occurred']);
            } catch (\Exception $exception) {
                // Manejar otras excepciones
                Log::error('Exception: ' . $exception->getMessage());
                return redirect()->back()->withErrors(['errors' => 'An unexpected error occurred']);
            } catch (ValidationException $exception) {
                //maneja la validacion
                $errors = $exception->validator->errors()->all();
                $errorString = implode(', ', $errors);
                Log::error('validation exception: ' . $errorString);
                return Redirect::back()->withErrors($errors);
            }
    }

    public function logout(Request $request){
        try{
            //cierra la sesion
            Auth::logout();
            $request->session()->invalidate();
            Log::info('User logged out');
            return redirect('/');
        } 
        catch (PDOException $exception) {
            // Manejar excepciones de PDO
            Log::error('PDOException: ' . $exception->getMessage());
            return redirect()->back()->withErrors(['errors' => 'An unexpected error occurred']);
        } 
        catch (\Exception $exception) {
            // Manejar otras excepciones
            Log::error('Exception: ' . $exception->getMessage());
            return redirect()->back()->withErrors(['errors' => 'An unexpected error occurred']);
        }
    }

    public function createUser(Request $request){
        //Valida los datos de entrada
        try {
            $request->validate([
                'name' => 'required|max:50|string',
                'email' => 'required|email|max:60|unique:users',
                'password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
                'c_password' =>'required|min:8|same:password',
                'phoneNumber' => 'required|numeric|digits:10'
            ]);

            //uso del api para el captcha
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => '6Lf0u14pAAAAAF0NoqzyqtdbRvoX9LXQcrRaDdVl',
                'response' => $request->input('g-recaptcha-response'),
            ]);

            $responseData = $response->json();

            //verifica si el captcha ha sido completado y si no devuelve un mensaje
            if (!$responseData['success']) {
                Log::error('Recaptcha error');
                return Redirect::back()->withErrors(['message' => 'Recaptcha error']);
            }

            //crea un usuario
            $user = new User;
            //busca a todos los usuarios
            $users = User::all();
            //verifica si hay usuarios
            if($users->isEmpty()){
                //si no hay ningun usuario, se le asigna el id 1000 y se le asigna el rol de admin
                $user->id = 1000;
                $user->rol_id = 3;
                $user->active = false;
            }  
            else{
                //si no solo se le asigna el usuario
                $user->rol_id = 2;
                $user->active = true;
            }

            //guarda los campos del request a campos del usuario
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone_number = $request->phoneNumber;
            //$user->code = rand(1000,9999);
            //Mail::to('egmr.49@gmail.com')->send(new SendUrlMail($User));

            //guarda el usuario
            if($user->save()){

                //si se guarda, manda el correo para que lo active
                Log::info('User created: ' . $user->name);
                SendUrlEmailJob::dispatch($user)->onQueue('emailsUrl');
                return redirect('/mailView');
            }

        } catch (ValidationException $exception) {
            //maneja la validacion
            $errors = $exception->validator->errors()->all();
            $errorString = implode(', ', $errors);
            Log::error('validation exception: ' . $errorString);
            return Redirect::back()->withErrors($errors);
        } catch (QueryException $exception) {
            //maneja el query
            Log::error('QueryException: ' . $exception->getMessage());
            return Redirect::back()->withErrors(['message' => 'An unexpected error occurred']);
        } catch (\PDOException $exception) {
            //maneja la excepcion pdo
            Log::error('PDOException: ' . $exception->getMessage());
            return Redirect::back()->withErrors(['message' => 'An unexpected error occurred']);
        } catch (\Exception $exception) {
            //maneja cualquier otra execption
            Log::error('Exception: ' . $exception->getMessage());
            return Redirect::back()->withErrors(['message' => 'An unexpected error occurred']);
        }
    }

    public function verifyCode(Request $request, Int $userId){
        try{
            //verifica la entrada de los datos
            $request->validate([
                "code"=>"required|numeric|digits:4",
            ]);

            $user = User::findOrFail($userId);

            //verifica si el codigo es igual al que se mando
            if(!Hash::check($request->code, $user->code)){
            //if($user->code != $request->code){
                //si no es igual, manda un mensaje de error
                return Redirect::back()->withErrors(['message' => 'El código es incorrecto']);
            }
            
            if($user->active == false){
                //si no esta activo, se activa
                $user->active = true;
            }

            if($user->save()){

                Auth::login($user);
                Log::info('User logged: ' . $user->name);
                if($user->rol_id != 3){
                    //si no es admin, lo manda a la vista normal
                    return redirect('/home/user');
                }
                //lo manda a la vista de administrador
                return redirect()->intended('/home/admin');
            }
        }
        catch(ValidationException $exception){
            //maneja la validacion
            $errors = $exception->validator->errors()->all();
            $errorString = implode(', ', $errors);
            Log::error('validation exception: ' . $errorString);
            return Redirect::back()->withErrors($errors);
        } catch (ModelNotFoundException $exception) {
            // Manejar la excepción cuando el usuario no se encuentra
            Log::error('ModelNotFoundException: ' . $exception->getMessage());
            return Redirect::back()->withErrors(['message' => 'An unexpected error occurred']); 
        } catch (\Exception $exception) {
            // Manejar otras excepciones inesperadas
            Log::error('Exception: ' . $exception->getMessage());
            return Redirect::back()->withErrors(['message' => 'An unexpected error occurred']);
        }
    }

    public function VerifyEmail(Request $request, Int $userId){
        try {
            //busca al usuario por el id
            $user = User::findOrFail($userId);

            //verifica que el usuario exista
            if($user){
                $user->active = true;
                if($user->save()){
                    //si se activa lo manda al login
                    Log::info('usuario activado ' . $user->name);
                    return redirect('/');
                }
            }   
        } catch (ModelNotFoundException $exception) {
            // Manejar la excepción cuando el usuario no se encuentra
            return Redirect::back()->withErrors(['message' => 'Usuario no encontrado']);
        } catch (\Exception $exception) {
            // Manejar otras excepciones inesperadas
            return Redirect::back()->withErrors(['message' => 'Se produjo un error inesperado']);
        }
    }

}
