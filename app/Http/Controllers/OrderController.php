<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Address;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Crea una nueva orden de venta.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Definir las reglas de validación para los datos de la solicitud
        $rules = [
            'user_id' => [
                'required',
                'exists:users,id', // Verifica que el user_id exista en la tabla de usuarios
            ],
            'billing_address' => 'required|array',
            'billing_address.street' => 'required|string',
            'billing_address.city' => 'required|string',
            'billing_address.state' => 'required|string',
            'billing_address.zip_code' => 'required|string',
            'shipping_address' => 'required|array',
            'shipping_address.street' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.zip_code' => 'required|string',
            'products' => 'required|array',
            'products.*.id' => [
                'required',
                'exists:products,id' // Verifica que el producto exista en la tabla de productos
            ],
            'products.*.quantity' => 'required|integer|min:1',
        ];

        // Crear un validador con los datos de la solicitud y las reglas definidas
        $validator = Validator::make($request->all(), $rules);

        // Verificar si la validación falló
        if ($validator->fails()) {
            // Retornar una respuesta de error con los detalles de la validación
            return response()->json([
                'status' => [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
            ], 400);
        }

        // Inicializar el monto total
        $totalAmount = 0;

        // Buscar el usuario basado en el user_id proporcionado
        $user = User::find($request->input('user_id'));

        // Crear la dirección de facturación
        $billingAddress = Address::create([
            'user_id' => $user->id,
            'street' => $request->input('billing_address.street'),
            'city' => $request->input('billing_address.city'),
            'state' => $request->input('billing_address.state'),
            'zip_code' => $request->input('billing_address.zip_code')
        ]);

        // Crear la dirección de envío
        $shippingAddress = Address::create([
            'user_id' => $user->id,
            'street' => $request->input('shipping_address.street'),
            'city' => $request->input('shipping_address.city'),
            'state' => $request->input('shipping_address.state'),
            'zip_code' => $request->input('shipping_address.zip_code')
        ]);

        // Calcular el monto total de la orden basado en los productos y sus cantidades
        foreach ($request->input('products') as $productData) {
            $product = Product::find($productData['id']);
            $totalAmount += $product->price * $productData['quantity'];
        }

        // Crear la orden con la información proporcionada
        $order = Order::create([
            'user_id' => $user->id,
            'billing_address_id' => $billingAddress->id,
            'shipping_address_id' => $shippingAddress->id,
            'total_amount' => $totalAmount,
        ]);

        // Adjuntar los productos a la orden con sus cantidades y precios
        foreach ($request->input('products') as $productData) {
            $product = Product::find($productData['id']);
            $order->products()->attach($product->id, [
                'quantity' => $productData['quantity'],
                'price' => $product->price
            ]);
        }

        // Retornar una respuesta de éxito con los detalles de la orden creada
        return response()->json([
            'status' => [
                'success' => true
            ],
            'data' => $order->load('user', 'billingAddress', 'shippingAddress', 'products'),
        ], 200);
    }

    /**
     * Obtiene los detalles de una orden existente.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Definir las reglas de validación para el ID de la orden
        $rules = [
            'id' => 'required|integer|exists:orders,id', // Verifica que el id exista en la tabla de órdenes
        ];

        // Crear un validador con el ID de la orden y las reglas definidas
        $validator = Validator::make(['id' => $id], $rules);

        // Verificar si la validación falló
        if ($validator->fails()) {
            // Retornar una respuesta de error con los detalles de la validación
            return response()->json([
                'status' => [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
            ], 400);
        }

        // Buscar la orden basado en el ID proporcionado y cargar las relaciones
        $order = Order::with('user', 'billingAddress', 'shippingAddress', 'products')->findOrFail($id);
        
        // Retornar una respuesta de éxito con los detalles de la orden encontrada
        return response()->json([
            'status' => [
                'success' => true
            ],
            'data' => $order->load('user', 'billingAddress', 'shippingAddress', 'products'),
        ], 200);
    }
}
