<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Jobs\SendOrderStatusUpdateEmail;
use App\Models\Order;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Enums\OrderStatusEnum;

use function Illuminate\Database\Eloquent\Relations\findOrFail;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public OrderService $os;
    /**
     * @var InvoiceService
     */
    public InvoiceService $is;

    public function __construct(OrderService $os, InvoiceService $is)
    {
        $this->os = $os;
        $this->is = $is;
    }

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function index()
    {
        return Order::with('items')
            ->where('user_id', auth()->user()->id)
            ->where('status', '!=', OrderStatusEnum::DELIVERED->value)
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function orderHistory()
    {
        return Order::with('items')
            ->where('user_id', auth()->user()->id)
            ->where('status', OrderStatusEnum::DELIVERED->value)
            ->get();
    }

    /**
     * Store a newly created resource in storage.
     * @param OrderStoreRequest $request
     * @param OrderService $os
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderStoreRequest $request)
    {
        $validated = $request->validated();
        DB::transaction(function () use ($validated) {
            $order = $this->os->createOrder($validated);
            $this->os->upsertOrderItems($order, $validated);
        });

        return response()->json(['message' => __('messages.order.created')], Response::HTTP_CREATED);
    }

    /**
     * @param OrderStoreRequest $request
     * @param $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(OrderStoreRequest $request, $orderId)
    {
        $validated = $request->validated();
        $order = Order::where('status', OrderStatusEnum::PENDING->value)->findOrFail($orderId);
        $this->authorize('update', $order);

        DB::transaction(function () use ($validated, $order) {
            $this->os->updateOrder($order, $validated);
            $this->os->upsertOrderItems($order, $validated);
            $this->os->deleteItems($order, $validated);
        });

        return response()->json(['message' => __('messages.order.updated')]);
    }

    /**
     * @param $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirmOrder($orderId)
    {
        try {
            $order = Order::with('items')->where('status', OrderStatusEnum::PENDING->value)->findOrFail($orderId);
            $order->update(['status' => OrderStatusEnum::PROCESSING->value]);
            $this->os->updateStock($order->items, $order->status);
            $this->is->generateOrderInvoice($order);
            return \response()->json(['message' => __('messages.order.confirmed')]);
        } catch (ModelNotFoundException $e) {
            return \response()->json(['message' => $e->getMessage()]);
        } catch (\Exception $e) {
            return \response()->json(['message' => __('messages.order.server_error')]);
        }
    }

    /**
     * @param $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelOrder($orderId)
    {
        try {
            $order = Order::with('items')->where('status', OrderStatusEnum::PROCESSING->value)->findOrFail($orderId);
            DB::transaction(function () use ($order) {
                $order->update(['status' => OrderStatusEnum::CANCELLED->value]);
                $this->os->updateStock($order->items, $order->status);
            });
            return \response()->json(['message' => __('messages.order.cancelled')]);
        } catch (ModelNotFoundException $e) {
            return \response()->json(['message' => $e->getMessage()]);
        } catch (\Exception $exception) {
            return \response()->json(['message' => __('messages.order.server_error')]);
        }
    }

    /**
     * admin only
     * @param $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus($orderId)
    {
        try {
            $order = Order::whereNotIn('status', [OrderStatusEnum::PENDING->value, OrderStatusEnum::CANCELLED->value, OrderStatusEnum::DELIVERED->value])->findOrFail($orderId);
            $nextStatus = $this->os->getNextStatusToUpdate($order->status);
            $order->update(['status' => $nextStatus]);
            $user = User::findOrFail($order->user_id);
            SendOrderStatusUpdateEmail::dispatch($user->email, $user->name, $order->order_number, $order->status);
            return \response()->json(['message' => __('messages.order.status_updated') .' to: '. $nextStatus]);
        } catch (ModelNotFoundException $e) {
            return \response()->json(['message' => $e->getMessage()]);
        } catch (\Exception $exception) {
            return \response()->json(['message' => __('messages.order.server_error')]);
        }
    }
}
