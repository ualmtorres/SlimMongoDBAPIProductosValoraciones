<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use MongoDB\BSON\ObjectId;

// Endpoint para crear un nuevo producto
$app->post($prefix . '/products', function (RequestInterface $request, ResponseInterface $response) use ($products) {
    $data = $request->getParsedBody();

    // Validar los datos de entrada
    if (!isset($data['name'], $data['description'], $data['price'], $data['categoryId'])) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    // Insertar el nuevo producto en la base de datos
    $result = $products->insertOne([
        'name' => $data['name'],
        'description' => $data['description'],
        'price' => $data['price'],
        'categoryId' => $data['categoryId']
    ]);

    // Devolver la respuesta con el ID del nuevo producto
    return createJsonResponse($response->withStatus(201), [
        'status' => 201,
        'message' => 'Product created',
        'productId' => (string) $result->getInsertedId()
    ]);
});

// Endpoint para obtener todos los productos o los productos de una categoría o por nombre
$app->get($prefix . '/products', function (RequestInterface $request, ResponseInterface $response) use ($products) {
    $queryParams = $request->getQueryParams();
    $filter = [];

    if (isset($queryParams['categoryId'])) {
        // Validar el ID de la categoría
        try {
            new ObjectId($queryParams['categoryId']);
            $filter['categoryId'] = $queryParams['categoryId'];
        } catch (Exception $e) {
            return createJsonResponse($response->withStatus(400), [
                'status' => 400,
                'message' => 'Invalid category ID'
            ]);
        }
    }

    if (isset($queryParams['name'])) {
        $filter['name'] = new \MongoDB\BSON\Regex($queryParams['name'], 'i');
    }

    $result = $products->find($filter)->toArray();

    // Convertir ObjectId a string
    $productsArray = array_map(function ($product) {
        $product['_id'] = (string) $product['_id'];
        return $product;
    }, $result);

    return createJsonResponse($response, [
        'status' => 200,
        'data' => $productsArray
    ]);
});

// Endpoint para obtener un producto por ID
$app->get($prefix . '/products/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($products) {
    $id = $args['id'];

    // Validar el ID
    try {
        new ObjectId($id);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid product ID'
        ]);
    }

    // Buscar el producto en la base de datos
    $product = $products->findOne(['_id' => new ObjectId($id)]);

    if (!$product) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Product not found'
        ]);
    }

    // Convertir ObjectId a string
    $product['_id'] = (string) $product['_id'];

    return createJsonResponse($response, [
        'status' => 200,
        'data' => $product
    ]);
});

// Endpoint para actualizar un producto por ID
$app->put($prefix . '/products/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($products) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    // Validar el ID
    try {
        new ObjectId($id);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid product ID'
        ]);
    }

    // Validar los datos de entrada
    if (!isset($data['name'], $data['description'], $data['price'], $data['categoryId'])) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    // Actualizar el producto en la base de datos
    $result = $products->updateOne(
        ['_id' => new ObjectId($id)],
        ['$set' => [
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'categoryId' => $data['categoryId']
        ]]
    );

    if ($result->getMatchedCount() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Product not found'
        ]);
    }

    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'Product updated'
    ]);
});

// Endpoint para eliminar un producto por ID
$app->delete($prefix . '/products/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($products, $reviews) {
    $id = $args['id'];

    // Validar el ID
    try {
        new ObjectId($id);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid product ID'
        ]);
    }

    // Eliminar el producto de la base de datos
    $result = $products->deleteOne(['_id' => new ObjectId($id)]);

    if ($result->getDeletedCount() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Product not found'
        ]);
    }

    // Eliminar los comentarios asociados al producto
    $reviews->deleteMany(['productId' => new ObjectId($id)]);

    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'Product and associated reviews deleted'
    ]);
});
