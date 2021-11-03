<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller;

use Farol360\AncoraEad\Controller;
use Farol360\AncoraEad\Mailer;
use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class UserController extends Controller
{
    protected $userModel;
    protected $mailer;

    public function __construct(View $view, FlashMessages $flash, Model $user, Mailer $mailer)
    {
        parent::__construct($view, $flash);
        $this->userModel = $user;
        $this->mailer = $mailer;
    }

    public function dashboard(Request $request, Response $response): Response
    {
        $user = $this->userModel->get();
        $courses = $this->userModel->getUserCourses((int)$user->id);
        $statusCodes = [
            1 => 'Aguardando Pagamento',
            2 => 'Em análise',
            3 => 'Paga',
            4 => 'Disponível',
            5 => 'Em disputa',
            6 => 'Devolvida',
            7 => 'Cancelada',
        ];
        $orders = $this->userModel->getUserOrders((int)$user->id);
        if (!empty($orders)) {
            foreach ($orders as $order) {
                if ($order->status >= 1 && $order->status <= 7) {
                    $order->status = $statusCodes[$order->status];
                }
            }
        }
        return $this->view->render($response, 'user/dashboard.twig', [
            'courses' => $courses,
            'orders' => $orders,
        ]);
    }

    public function profile(Request $request, Response $response): Response
    {
        $user = $this->userModel->get();
        if (empty($request->getParsedBody())) {
            $user = $this->userModel->get((int)$user->id);
            return $this->view->render($response, 'user/profile.twig', [
                'user' => $user,
            ]);
        }
        $name = $request->getParsedBody()['name'];
        $email = strtolower($request->getParsedBody()['email']);
        $password = $request->getParsedBody()['password'];
        if (!empty($request->getParsedBody()['nascimento'])) {
            $nascimento = date('Y-m-d', strtotime($request->getParsedBody()['nascimento']));
        }
        $cpf = $request->getParsedBody()['cpf'];
        $telArea = $request->getParsedBody()['tel_area'];
        $telNumero = $request->getParsedBody()['tel_numero'];
        $endRua = $request->getParsedBody()['end_rua'];
        $endNumero = $request->getParsedBody()['end_numero'];
        $endComplemento = $request->getParsedBody()['end_complemento'];
        $endBairro = $request->getParsedBody()['end_bairro'];
        $endCidade = $request->getParsedBody()['end_cidade'];
        $endEstado = $request->getParsedBody()['end_estado'];
        $endCep = $request->getParsedBody()['end_cep'];
        $newPassword = $request->getParsedBody()['new_password'];

        if (hash_equals($user->password, crypt($password, $user->password))) {
            $this->userModel->update(
                $user->id,
                $email,
                $name,
                $user->role_id,
                $nascimento,
                $cpf,
                $telArea,
                $telNumero,
                $endRua,
                $endNumero,
                $endComplemento,
                $endBairro,
                $endCidade,
                $endEstado,
                $endCep,
                $newPassword
            );
            $this->flash->addMessage('success', 'Perfil alterado com sucesso!');
            return $this->httpRedirect($request, $response, '/users/profile');
        }
        $this->flash->addMessage('danger', 'Senha incorreta!');
        return $this->httpRedirect($request, $response, '/users/profile');
    }

    public function signIn(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {
            return $this->view->render($response, 'user/signin.twig');
        } else {
            $email = strtolower($request->getParsedBody()['email']);
            $password = $request->getParsedBody()['password'];

            if (!User::login($email, $password)) {
                $this->flash->addMessage('errorLogin', 'Usuário ou senha errados!');
                return $this->httpRedirect($request, $response, '/users/signin');
            } else {
                $user = $this->userModel->get();
                $user = $this->userModel->get((int)$user->id);
                if ($user->active == 0) {
                    if (isset($_SESSION['user'])) {
                        User::setSessionId(null);
                    }
                    User::logout();
                    $this->flash->addMessage('danger', 'Seu endereço de email não foi verificado.');
                    $return = '/users/recover';
                } else {
                    if (isset($_SESSION['return'])) {
                        $return = $_SESSION['return'];
                        unset($_SESSION['return']);
                    } else {
                        $return = '/users/profile';
                    }
                }
                return $this->httpRedirect($request, $response, $return);
            }
        }
    }

    public function signOut(Request $request, Response $response): Response
    {
        if (isset($_SESSION['user'])) {
            User::setSessionId(null);
        }
        User::logout();
        return $this->httpRedirect($request, $response);
    }

    public function signUp(Request $request, Response $response): Response
    {
        $bodyData = $request->getParsedBody();
        if (empty($bodyData)) {
            return $this->view->render($response, 'user/signup.twig');
        } else {
            if (!empty($this->userModel->getByEmail($bodyData['email']))) {
                $this->flash->addMessage('danger', sprintf(
                    'O email %s já foi cadastrado!',
                    strtolower($bodyData['email'])
                ));
                return $this->httpRedirect($request, $response, '/users/signin');
            } else {
                if ($bodyData['password'] == $bodyData['confirm_password']) {
                    if (User::isAuth()) {
                        User::setSessionId(null);
                        User::logout();
                    }
                    // role = user
                    $bodyData['role_id'] = 4;
                    $user = $this->entityFactory->createUser($bodyData);
                    $this->userModel->add($user);
                    if (User::sendVerification($this->mailer, $user->email)) {
                        $this->flash->addMessage(
                            'success',
                            'Um link de verificação foi enviado ao seu endereço de email.'
                        );

                        return $this->httpRedirect($request, $response, '/users/signin');
                    } else {
                        $this->flash->addMessage(
                            'danger',
                            'Erro ao enviar email.'
                        );
                        return $this->httpRedirect($request, $response, '/users/signin');
                    }
                } else {
                    $this->flash->addMessage('danger', 'As senhas não são iguais.');
                    return $this->httpRedirect($request, $response, '/users/signup');
                }
            }
        }
    }

    public function recover(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {
            return $this->view->render($response, 'user/recover.twig');
        } else {
            $email = strtolower($request->getParsedBody()['email']);
            if (User::sendRecover($this->mailer, $email)) {
                $this->flash->addMessage(
                    'success',
                    'Um link de recuperação foi enviado ao seu endereço de email.'
                );
            } else {
                $this->flash->addMessage('danger', 'Problema ao enviar email.');
            }
            return $this->httpRedirect($request, $response, '/users/recover');
        }
    }

    public function recoverPassword(Request $request, Response $response, array $args): Response
    {
        if (empty($request->getParsedBody())) {
            if (User::getUserByToken($args['token'])) {
                return $this->view->render($response, 'user/password.twig', [
                    'token' => $args['token']
                ]);
            } else {
                $this->flash->addMessage('danger', 'Token incorreto!');
                return $this->httpRedirect($request, $response, '/users/recover');
            }
        } else {
            $token = $request->getParsedBody()['token'];
            $password = $request->getParsedBody()['password'];
            $confirmPassword = $request->getParsedBody()['confirm_password'];

            if ($user = User::getUserByToken($token)) {
                if ($password == $confirmPassword) {
                    $this->userModel->update(
                        $user->id,
                        $user->email,
                        $user->name,
                        $user->role_id,
                        $user->nascimento,
                        $user->cpf,
                        $user->tel_area,
                        $user->tel_numero,
                        $user->end_rua,
                        $user->end_numero,
                        $user->end_complemento,
                        $user->end_bairro,
                        $user->end_cidade,
                        $user->end_estado,
                        $user->end_cep,
                        $password
                    );
                    $this->userModel->verify((int)$user->id);
                    $this->flash->addMessage('success', 'Senha alterada com sucesso.');
                    return $this->httpRedirect($request, $response, '');
                } else {
                    $this->flash->addMessage('danger', 'As senhas não são iguais.');
                    return $this->httpRedirect($request, $response, '/users/recover');
                }
            } else {
                $this->flash->addMessage('danger', 'Token errado!');
                return $this->httpRedirect($request, $response, '/users/recover');
            }
        }
    }

    public function verify(Request $request, Response $response, array $args): Response
    {
        if ($user = User::getUserByToken($args['token'])) {
            if (User::isAuth()) {
                User::setSessionId(null);
                User::logout();
            }
            $_SESSION['user'] = (array) $user;
            $sessionId = session_id();
            User::setSessionId($sessionId);
            $_SESSION['user']['session_id'] = $sessionId;
            $this->userModel->verify((int)$user->id);

            $this->flash->addMessage('success', 'Email verificado com sucesso!');
            return $this->httpRedirect($request, $response, '/users/profile');
        } else {
            $this->flash->addMessage('danger', 'Token incorreto!');
            return $this->httpRedirect($request, $response, '/users/recover');
        }
    }
}
