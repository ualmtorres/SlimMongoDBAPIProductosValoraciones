<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use MongoDB\BSON\ObjectId;

// Endpoint para añadir un comentario a un producto
$app->post($prefix . '/reviews', function (RequestInterface $request, ResponseInterface $response) use ($reviews) {
    $data = $request->getParsedBody();

    // Validar los datos de entrada
    if (!isset($data['productId'], $data['userId'], $data['username'], $data['rating'], $data['comment'])) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    // Validar el ID del producto
    try {
        new ObjectId($data['productId']);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid product ID'
        ]);
    }

    // Añadir el comentario a la colección de reviews
    $result = $reviews->insertOne([
        'productId' => new ObjectId($data['productId']),
        'userId' => new ObjectId($data['userId']),
        'username' => $data['username'],
        'rating' => $data['rating'],
        'comment' => $data['comment'],
        'createdAt' => new \MongoDB\BSON\UTCDateTime()
    ]);

    return createJsonResponse($response->withStatus(201), [
        'status' => 201,
        'message' => 'Comment added',
        'commentId' => (string) $result->getInsertedId()
    ]);
});

// Endpoint para obtener los comentarios de un producto o por usuario
$app->get($prefix . '/reviews', function (RequestInterface $request, ResponseInterface $response) use ($reviews) {
    $queryParams = $request->getQueryParams();
    $filter = [];

    if (isset($queryParams['productId'])) {
        // Validar el ID del producto
        try {
            new ObjectId($queryParams['productId']);
            $filter['productId'] = new ObjectId($queryParams['productId']);
        } catch (Exception $e) {
            return createJsonResponse($response->withStatus(400), [
                'status' => 400,
                'message' => 'Invalid product ID'
            ]);
        }
    }

    if (isset($queryParams['userId'])) {
        // Validar el ID del usuario
        try {
            new ObjectId($queryParams['userId']);
            $filter['userId'] = new ObjectId($queryParams['userId']);
        } catch (Exception $e) {
            return createJsonResponse($response->withStatus(400), [
                'status' => 400,
                'message' => 'Invalid user ID'
            ]);
        }
    }

    // Buscar los comentarios en la colección de reviews
    $reviews = $reviews->find($filter)->toArray();

    // Convertir ObjectId a string y formatear fechas en los comentarios
    $reviewsArray = array_map(function ($comment) {
        $comment['_id'] = (string) $comment['_id'];
        $comment['productId'] = (string) $comment['productId'];
        $comment['userId'] = (string) $comment['userId'];
        $comment['createdAt'] = $comment['createdAt']->toDateTime()->format('Y-m-d\TH:i:s\Z');
        if (isset($comment['updatedAt'])) {
            $comment['updatedAt'] = $comment['updatedAt']->toDateTime()->format('Y-m-d\TH:i:s\Z');
        }
        return $comment;
    }, $reviews);

    return createJsonResponse($response, [
        'status' => 200,
        'data' => $reviewsArray
    ]);
});

// Endpoint para actualizar un comentario
$app->put($prefix . '/reviews/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($reviews) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    // Validar el ID del comentario
    try {
        new ObjectId($id);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid comment ID'
        ]);
    }

    // Validar que el ID del usuario sea un ObjectId
    try {
        new ObjectId($data['userId']);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid user ID'
        ]);
    }

    // Validar los datos de entrada
    if (!isset($data['userId'], $data['rating'], $data['comment'])) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    // Verificar que el comentario pertenece al usuario
    $comment = $reviews->findOne(['_id' => new ObjectId($id), 'userId' => new ObjectId($data['userId'])]);
    if (!$comment) {
        return createJsonResponse($response->withStatus(403), [
            'status' => 403,
            'message' => 'Forbidden: You can only update your own reviews'
        ]);
    }

    // Actualizar el comentario en la base de datos
    $result = $reviews->updateOne(
        ['_id' => new ObjectId($id)],
        ['$set' => [
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'updatedAt' => new \MongoDB\BSON\UTCDateTime()
        ]]
    );

    if ($result->getMatchedCount() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Comment not found'
        ]);
    }

    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'Comment updated'
    ]);
});

// Endpoint para eliminar un comentario
$app->delete($prefix . '/reviews/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($reviews) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    // Validar el ID del comentario
    try {
        new ObjectId($id);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid comment ID'
        ]);
    }

    // Validar el ID del usuario
    if (!isset($data['userId'])) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    // Validar que el ID del usuario sea válido
    try {
        new ObjectId($data['userId']);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid user ID'
        ]);
    }

    // Verificar que el comentario pertenece al usuario
    $comment = $reviews->findOne(['_id' => new ObjectId($id), 'userId' => new ObjectId($data['userId'])]);
    if (!$comment) {
        return createJsonResponse($response->withStatus(403), [
            'status' => 403,
            'message' => 'Forbidden: You can only delete your own reviews'
        ]);
    }

    // Eliminar el comentario de la base de datos
    $result = $reviews->deleteOne(['_id' => new ObjectId($id)]);

    if ($result->getDeletedCount() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Comment not found'
        ]);
    }

    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'Comment deleted'
    ]);
});
