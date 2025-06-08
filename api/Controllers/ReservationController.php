<?php

namespace App\Controllers;

use App\Utility\_DataAccess;
use App\Utility\Result;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ReservationController extends BaseController
{
    private _DataAccess $dataAccess;

    public function __construct(_DataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function addReservation(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $requestBody = $request->getParsedBody();

            $reservationDate = $requestBody['reservation_date'] ?? null;
            $start = $requestBody['start'] ?? null;
            $end = $requestBody['end'] ?? null;

            $this->dataAccess->debug = true;
            $reservationId = $this->dataAccess->add(
                table: 'reservations',
                requestData: [
                    'user_id' => $this->userId,
                    'reservation_date' => $reservationDate,
                    'start_time' => $start,
                    'end_time' => $end
                ]
            );

            if($reservationId) {
                $result->setSuccessResult([
                    'reservation_id' => $reservationId,
                ]);
            }
            else {
                $result->setGenericError($reservationId);
            }
        }
        else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function deleteReservation(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $requestBody = $request->getParsedBody();

            $reservationId = $requestBody['reservation_id'] ?? null;

            if($reservationId) {

                $deleteItemsResult = $this->dataAccess->delete(
                    table: 'reservations_items',
                    args: ['reservation_id' => $reservationId],
                );
                $deleteResult = $this->dataAccess->delete(
                    table: 'reservations',
                    args: ['reservation_id' => $reservationId],
                );

                if($deleteResult && $deleteItemsResult) {
                    $result->setSuccessResult();
                }
                else {
                    $result->setGenericError();
                }

            }
            else {
                $result->setInvalidParameters();
            }

        }
        else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function getReservations(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if(!$this->validateToken($request)) {
            $result->setUnauthorized();
        }
        else {

            $requestBody = $request->getParsedBody();

            if(!isset($requestBody['start']) && !isset($requestBody['window_type'])) {
                $result->setUnauthorized();
            }
            else {

                $start = $requestBody['start'] ?? null;
                $end = $requestBody['end'] ?? null;
                $windowType = $requestBody['window_type'];
                $reservations = null;
                $reservations_items = null;

                switch($windowType) {
                    case 'day':
                        $reservations = $this->dataAccess->get(
                            table: 'reservations',
                            args: ['reservation_date' => $start],
                            fields: ['reservations.*', 'users.user_name'],
                            join: ['users' => 'user_id'],
                        );
                        break;
                    case 'week':
                        if(!$end) {
                            $result->setInvalidParameters(['no end']);
                        }
                        else {
                            $reservations = $this->dataAccess->get(
                                table: 'reservations',
                                args: [
                                    'reservation_date' => ">$start",
                                    'reservations.reservation_date' => "<$end",
                                ],
                                fields: ['reservations.*', 'users.user_name'],
                                join: ['users' => 'user_id'],
                            );
                        }
                        break;
                    default:
                        $result->setInvalidParameters(['no type']);
                        break;
                }

                if($reservations) {
                    $reservations_items = $this->dataAccess->get(
                        table: 'reservations_items',
                        args: ['services.deleted' => 0],
                        fields: ['reservations_items.*', 'services.service_name', 'services.price', 'services.duration'],
                        join: ['services' => 'service_id'],
                        in: ['reservation_id' => array_column($reservations, 'reservation_id')],
                    );
                }

                $data = ['reservations' => $reservations, 'reservations_items' => $reservations_items];
                $result->statusCode === 200 ?
                    $result->setSuccessResult($data) :
                    $result->setData($data);
            }

        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function updateReservation(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if($this->validateToken($request)) {

            $requestBody = $request->getParsedBody();

            $reservation_id = $requestBody['reservation_id'] ?? null;
            $user_id = $requestBody['user_id'] ?? null;
            $reservation_date = $requestBody['reservation_date'] ?? null;
            $start_time = $requestBody['start_time'] ?? null;
            $end_time = $requestBody['end_time'] ?? null;
            $client_name = $requestBody['client_name'] ?? null;
            $phone_number = $requestBody['phone_number'] ?? null;
            $description = $requestBody['description'] ?? null;
            $reservation_items = $requestBody['reservation_items'] ?? null;

            if ($reservation_id &&
                $user_id &&
                $reservation_date &&
                $start_time &&
                $end_time) {

                $updateResult = $this->dataAccess->update(
                    table: 'reservations',
                    args: ['reservation_id' => $reservation_id],
                    requestData: [
                        'reservation_date' => $reservation_date,
                        'start_time' => $start_time,
                        'end_time' => $end_time,
                        'client_name' => $client_name,
                        'phone_number' => $phone_number,
                        'description' => $description,
                        'user_id' => $user_id
                    ]
                );

                if($reservation_items){
                    $this->dataAccess->delete(
                        table: 'reservations_items',
                        args: ['reservation_id' => $reservation_id],
                    );
                    $this->dataAccess->add(
                        table: 'reservations_items',
                        requestData: array_map(
                            fn($item) => ['service_id' => $item, 'reservation_id' => $reservation_id, 'quantity' => 1],
                            $reservation_items
                        ),
                    );
                }

                if($updateResult) {
                    $result->setSuccessResult();
                }
                else {
                    $result->setGenericError();
                }

            }
            else {
                $result->setInvalidParameters();
            }

        }
        else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}