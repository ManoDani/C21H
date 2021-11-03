<?php
declare(strict_types=1);

namespace Farol360\AncoraEad\Controller;

use Farol360\AncoraEad\Controller;
use Farol360\AncoraEad\Model;
use Farol360\AncoraEad\Model\EntityFactory;
use Farol360\AncoraEad\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class OrderController extends Controller
{
    protected $courseModel;
    protected $entityFactory;
    protected $orderModel;
    protected $pagseguro;
    protected $userCourseModel;
    protected $userModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $order,
        Model $course,
        Model $userCourse,
        Model $user,
        array $pagseguro,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->orderModel = $order;
        $this->courseModel = $course;
        $this->userCourseModel = $userCourse;
        $this->userModel = $user;
        $this->pagseguro = $pagseguro;
        $this->entityFactory = $entityFactory;
    }

    public function cart(Request $request, Response $response, array $args): Response
    {
        $id = intval($args['id']);
        $course = $this->courseModel->get($id);
        if (!$course) {
            $this->flash->addMessage('danger', 'Curso não encontrado.');
            return $this->httpRedirect($request, $response, '/courses');
        }

        $reference = strtoupper('PS' . uniqid());
        $url = 'https://sandbox.pagseguro.uol.com.br/v2/checkout/payment.html';
        if ($this->pagseguro['env'] == 'production') {
            $url = 'https://pagseguro.uol.com.br/v2/checkout/payment.html';
        }

        $user = $this->userModel->get();
        $order = $this->entityFactory->createOrder([
            'reference' => $reference,
            'course_id' => $course->id,
            'user_id' => $user->id,
            'amount' => $course->price,
        ]);
        $this->orderModel->add($order);

        return $this->view->render($response, 'order/cart.twig', [
            'course' => $course,
            'pagseguro' => $this->pagseguro,
            'pagseguro_url' => $url,
            'reference' => $reference,
        ]);
    }

    public function pagSeguro(Request $request, Response $response): Response
    {
        if (empty($request->getParsedBody())) {
            $transactionId = $request->getQueryParams()['transaction_id'];

            if (strlen($transactionId) == 36) {
                if ($this->pagseguro['env'] == 'production') {
                    $url = 'https://ws.pagseguro.uol.com.br/v3/transactions/';
                    $token = $this->pagseguro['production']['token'];
                } else {
                    $url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/';
                    $token = $this->pagseguro['sandbox']['token'];
                }

                $url .= $transactionId;
                $url .= '?email=' . $this->pagseguro['email'];
                $url .= '&token=' . $token;

                $responseXml = file_get_contents($url);
                $responseTransaction = new \SimpleXMLElement($responseXml);

                if (!empty($responseTransaction)) {
                    $order = $this->orderModel->getByReference($responseTransaction->reference);
                    if (!empty($order)) {
                        if ($order->amount == (string) $responseTransaction->grossAmount) {
                            $this->orderModel->update(
                                $order->id,
                                $order->reference,
                                $order->course_id,
                                $order->user_id,
                                $order->amount,
                                (string) $responseTransaction->code,
                                (int) $responseTransaction->status,
                                $responseXml
                            );
                            if ((int) $responseTransaction->status == 3) {
                                $this->userCourseModel->add($order->user_id, $order->course_id);
                            }
                        }
                    }
                }
            }
            return $this->httpRedirect($request, $response, '/courses');
        }
        $notificationCode = $request->getParsedBody()['notificationCode'];
        $notificationType = $request->getParsedBody()['notificationType'];

        if (strlen($notificationCode) == 39) {
            if ($this->pagseguro['env'] == 'production') {
                $url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/';
                $token = $this->pagseguro['production']['token'];
            } else {
                $url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/';
                $token = $this->pagseguro['sandbox']['token'];
            }
            $url .= $notificationCode;
            $url .= '?email=' . $this->pagseguro['email'];
            $url .= '&token=' . $token;

            $responseNotificationXml = file_get_contents($url);
            $responseNotification = new \SimpleXMLElement($responseNotificationXml);

            if (!empty($responseNotification)) {
                $order = $this->orderModel->getByReference($responseNotification->reference);
                if (!empty($order)) {
                    if ($order->amount == (string) $responseNotification->grossAmount) {
                        $this->orderModel->update(
                            $order->id,
                            $order->reference,
                            $order->course_id,
                            $order->user_id,
                            $order->amount,
                            $order->transaction,
                            (int) $responseNotification->status,
                            $responseNotificationXml
                        );
                        if ((int) $responseNotification->status == 3) {
                            $this->userCourseModel->add($order->user_id, $order->course_id);
                        }
                    }
                }
            }
        }
    }
}
