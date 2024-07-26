<?php

namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use App\Models\PerfilesModel;
use App\Models\PublicacionesModel;

class Sesion extends BaseController
{
    #ayudantes para tratar el baseurl y el form (register)
    protected $helpers = ['url', 'form'];

    public function index()
	{
		$mensaje = session('mensaje');
		return view('login', ["mensaje" => $mensaje]);
	}

    public function register()
    {
        return view('register');
    }
    #Aca creamos el usuario(register)
    public function create(){

        $validation = $this->validate([
            'nombre' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Se necesita tu nombre.',
                ],
            ],

            'apellido' => [
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Se necesita tu apellido.',
                ],
            ],

            'cedula' => [
                'rules'  => 'required|min_length[7]|max_length[8]|is_unique[usuarios.cedula]|numeric',
                'errors' => [
                    'is_unique' => 'Esta cédula ya ha sido elegida.',
                    'numeric' => 'La cédula debe ser un número.',
                    'min_length' => 'El campo cédula debe tener al menos 7 caracteres.',
                ],
            ],

            'usuario' => [
                'rules'  => 'required|is_unique[usuarios.usuario]|regex_match[/^'.$_POST['cedula'].'.+$/]',
                'errors' => [
                    'required' => 'Se necesita tu usuario.',
                    'is_unique' => 'Este usuario ya ha sido elegido.',
                    'regex_match' => 'El usuario debe comenzar con la cédula.',
                ],
            ],    
                    
            'password' => [
                'rules'  => 'required|min_length[8]|max_length[20]',
                'errors' => [
                    'required' => 'Password is required.',
                    'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
                    'max_length' => 'La contraseña no debe tener mas de 20 caracteres.',
                ],
            ],
            'telefono' => [
                'rules' => 'required|exact_length[11]|is_unique[usuarios.telefono]|',
                'errors' => [
                    'exact_length' => 'El número de teléfono debe tener 11 dígitos',
                    'is_unique' => 'Este número de teléfono ya ha sido elegido.',
                ],
            ],

            
            
        ]);

        

        if(!$validation){

            return redirect()->to(base_url('register'))->with('errors', $this->validator->getErrors())->withInput();

        }else{
            //Registro en la database
            $nombre = $this->request->getPost('nombre');
            $apellido = $this->request->getPost('apellido');
            $cedula = $this->request->getPost('cedula');
            $usuario = $this->request->getPost('usuario');
            $password = $this->request->getPost('password');
            $telefono = $this->request->getPost('telefono');
            $biografia = $this->request->getPost('biografia');

            $data = [
               'nombre'=>$nombre,
               'apellido'=>$apellido,
                'cedula'=>$cedula,
                'usuario'=>$usuario,
               'password'=> password_hash($password, PASSWORD_BCRYPT),
               //agregue esta linea para la variable del telefono 
               'telefono'=>$telefono,
               'biografia'=>$biografia,
            ];
            if ($usuario == '28447447anthony' || $usuario == '28132505liz' || $usuario == '27042470isaac' || $usuario == '28305519Carlos') {
                $data['roles'] = 1;
            } else {
                $data['roles'] = 0;
                echo "Rol 0 asignado";
            }

            $UsuarioModel = new UsuarioModel();
            $query = $UsuarioModel->insert($data);
            if ($query) {
                // Obtener el ID del usuario recién insertado
                $id_usuario = $UsuarioModel->insertID();
        
                // Crear un registro en la tabla 'perfiles' relacionado con el usuario
                $PerfilModel = new PerfilesModel();
                $PerfilModel->insert(array('id_usuarios' => $id_usuario));
        
                $message = session()->getFlashdata('Exito');
                $alertMessage = 'Has sido registrado correctamente';

                $alertData = [
                    'message' => $message,
                    'alert_message' => $alertMessage
                ];

                session()->setFlashdata('alert', $alertData);

                return redirect()->to(base_url('login'));


                                

            } else {
                return redirect()->to(base_url('register'));
            }
            
        }
    }

    protected $session;
    #Aca verificamos el login(index) ta dudosa junto a las rutas
    public function login(){

        $Usuario = new UsuarioModel();
        $usuario = $this->request->getPost('usuario');
        $password = $this->request->getPost('password');
    
        $datosUsuario = $Usuario->where(['usuario' => $usuario])->first();
    
        if (!empty($datosUsuario) && password_verify($password, $datosUsuario['password'])) {
            $session = session();
            $session->set("usuario", $datosUsuario);
            //use ese print para mostrar los datos de sesion
            // print_r ($session->get());
            // die();
            return redirect()->to(base_url('inicio'));
        }  else {
            session()->setFlashdata('Error', 'Usuario o contraseña no válidas');
            return redirect()->to(base_url('login'))->with('Error', 'El usuario o la contraseña son incorrectas :´(');
        }
        // esto va en controlador sesion lo puedes colocar al final 
    }

    public function logout(){

        $session = session();
        $session->destroy();
        return redirect()->to('/sesion/login');

    }

}


    

// public function login()
//     {
//         $correo = trim($this->request->getPost('email'));
//         $password = trim($this->request->getPost('password'));

//         $Usuario = new LoginModels();

//         $datosUsuario = $Usuario->obtenerUsuarios(['email'=>$correo]);

//         if(!count($datosUsuario) > 0 ){
//             return redirect()->back()->with('msgfalse', 'Usuario no registrado en el sistema');

//         }elseif (!password_verify($password, $datosUsuario[0]['password'])) {
//             return redirect()->back()->with('msgfalse', 'Contraseña invalida');
//         } else{

//             $data = [
//                 "id_usuario" => $datosUsuario[0]['id_usuario'],
//                 "nombre"   => $datosUsuario[0]['nombre'],
//                 "email"     => $datosUsuario[0]['email'],
//                 "password" =>  $datosUsuario[0]['password'],
//                 "genero"      => $datosUsuario[0]['genero'],
//                 "fecha"    => $datosUsuario[0]['fecha'],
//                 "logged_in" =>TRUE
//             ];

//             $session = session();
//             $session->set($data);
            

//             return redirect()->to(base_url('/'));

//         }

        
//     }







// namespace App\Controllers;
// use App\Controllers\BaseController;
// use App\Models\UsuarioModel;

// class sesion extends BaseController
// {
//     public function inicio()
// {
//     // Verifica si el usuario ha iniciado sesión
//     if ($this->isLoggedIn()) {
//         // Si el usuario ha iniciado sesión, redirigirlo a la página principal
//         return redirect()->to('inicio');
//     }

//     // Si el formulario de inicio de sesión no ha sido enviado, muestra la vista del formulario
//     if (!$this->request->getPost()) {
//         return view('index');
//     }

//     // Verifica si el formulario de inicio de sesión ha sido enviado
//     if ($this->request->getMethod() == 'post') {
//         // Valida los campos del formulario de inicio de sesión
//         // ...

//         // Obtiene las credenciales del formulario de inicio de sesión
//         $usuario = $this->request->getPost('usuario');
//         $password = $this->request->getPost('password');

//         // Verifica si las credenciales del usuario son válidas
//         if ($this->model->attemptLogin($usuario, $password)) {
//             // Si las credenciales son válidas, inicia sesión en la aplicación
//             $session = session();
//             $session->set('isLoggedIn', true);

//             // Redirige al usuario a la página principal
//             return redirect()->to('index');
//         } else {
//             // Si las credenciales no son válidas, muestra un mensaje de error
//             // ...
//         }
//     }
// }

//     public function obtenerUsuario($usuario, $password)
//     {
//         $model = new UsuarioModel();
//         $usuario = $model->where('usuario', $usuario)->first();

//         if (password_verify($password, $usuario['password'])) {
//             return $usuario;
//         } else {
           
//         }
//     }

//     protected $model;

//     public function __construct()
//     {
//         $this->model = new UsuarioModel();
//     }

 