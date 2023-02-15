<?php

namespace App\Anser\Services\V2;

use SDPMlab\Anser\Service\SimpleService;
use SDPMlab\Anser\Service\ActionInterface;
use Psr\Http\Message\ResponseInterface;
use SDPMlab\Anser\Exception\ActionException;
use SDPMlab\Anser\Service\Action;

class OrderService extends SimpleService
{
    protected $serviceName = "order_service";

    protected $retry      = 1;
    protected $retryDelay = 1;
    protected $timeout    = 10.0;

    /**
     * Get all order
     *
     * @param integer $u_key
     * @param integer|null $limit
     * @param integer|null $offset
     * @param string|null $isDesc
     * @return ActionInterface
     */
    public function getAllOrder(
        int $u_key,
        ?int $limit = null,
        ?int $offset = null,
        ?string $isDesc = null
    ): ActionInterface {
        $action = $this->getAction("GET", "/api/v2/order");

        $payload = [];

        if (!is_null($limit)) {
            $payload["limit"]  = $limit;
        }
        if (!is_null($offset)) {
            $payload["offset"] = $offset;
        }
        if (!is_null($isDesc)) {
            $payload["isDesc"] = $isDesc;
        }

        if (!empty($payload)) {
            $action->addOption("query", $payload);
        }

        $action->addOption("headers", [
            "X-User-Key" => $u_key
        ]);

        $action->doneHandler(
            function (
                ResponseInterface $response,
                Action $action
            ) {
                $resBody = $response->getBody()->getContents();
                $data    = json_decode($resBody, true);
                $action->setMeaningData($data["data"]);
            }
        )
            ->failHandler(
                function (
                    ActionException $e
                ) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData(["message" => $e->getMessage()]);
                }
            );
        return $action;
    }

    /**
     * Get order by order_key
     *
     * @param integer $u_key
     * @param string $order_key
     * @return ActionInterface
     */
    public function getOrder(int $u_key, string $order_key): ActionInterface
    {
        $action = $this->getAction("GET", "/api/v2/order/{$order_key}")
            ->addOption("headers", [
                    "X-User-Key" => $u_key
                ])
            ->doneHandler(
                function (
                    ResponseInterface $response,
                    Action $action
                ) {
                    $resBody = $response->getBody()->getContents();
                    $data    = json_decode($resBody, true);
                    $action->setMeaningData($data["data"]);
                }
            )
            ->failHandler(
                function (
                    ActionException $e
                ) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData(["message" => $e->getMessage()]);
                }
            );
        return $action;
    }

    /**
     * Create order
     *
     * @param integer $u_key
     * @param integer $p_key
     * @param integer $amount
     * @param integer $price
     * @return ActionInterface
     */
    public function createOrder(int $u_key, int $p_key, int $amount, int $price): ActionInterface
    {
        $action = $this->getAction("POST", "/api/v2/order")
            ->addOption("json", [
                "p_key"  => $p_key,
                "price"  => $price,
                "amount" => $amount
            ])
            ->addOption("headers", [
                "X-User-Key" => $u_key
            ])
            ->doneHandler(
                function (
                    ResponseInterface $response,
                    Action $action
                ) {
                    $resBody = $response->getBody()->getContents();
                    $data    = json_decode($resBody, true);
                    $action->setMeaningData($data["orderID"]);
                }
            )
            ->failHandler(
                function (
                    ActionException $e
                ) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData(["message" => $e->getMessage()]);
                }
            );
        return $action;
    }

    /**
     * Update order
     *
     * @param integer $u_key
     * @param string $order_key
     * @param integer|null $p_key
     * @param integer|null $amount
     * @param integer|null $price
     * @param string|null $status
     * @return ActionInterface
     */
    public function updateOrder(
        int $u_key,
        string $order_key,
        ?int $p_key = null,
        ?int $amount = null,
        ?int $price = null,
        ?string $status = null
    ): ActionInterface {
        $action = $this->getAction("PUT", "/api/v2/order/{$order_key}")
            ->addOption("json", [
                "p_key"  => $p_key,
                "amount" => $amount,
                "price"  => $price,
                "status" => $status,
            ])
            ->addOption("headers", [
                "X-User-Key" => $u_key
            ])
           ->doneHandler(
               function (
                   ResponseInterface $response,
                   Action $action
               ) {
                   $resBody = $response->getBody()->getContents();
                   $data    = json_decode($resBody, true);
                   $action->setMeaningData($data["status"]);
               }
           )
            ->failHandler(
                function (
                    ActionException $e
                ) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData(["message" => $e->getMessage()]);
                }
            );
        return $action;
    }

    /**
     * Delete order
     *
     * @param string $order_key
     * @return ActionInterface
     */
    public function deleteOrder(string $order_key): ActionInterface
    {
        $action = $this->getAction("DELETE", "/api/v2/order/{$order_key}")
           ->doneHandler(
               function (
                   ResponseInterface $response,
                   Action $action
               ) {
                   $resBody = $response->getBody()->getContents();
                   $data    = json_decode($resBody, true);
                   $action->setMeaningData($data["data"]);
               }
           )
            ->failHandler(
                function (
                    ActionException $e
                ) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData(["message" => $e->getMessage()]);
                }
            );
        return $action;
    }
}
