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
}