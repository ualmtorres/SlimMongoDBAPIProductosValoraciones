<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use MongoDB\BSON\ObjectId;

// Endpoint para crear una nueva categoría
$app->post($prefix . '/categories', function (RequestInterface $request, ResponseInterface $response) use ($categories) {
    $data = $request->getParsedBody();

    // Validar los datos de entrada
    if (!array_key_exists('name', $data) || !array_key_exists('description', $data) || !array_key_exists('parentId', $data)) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    // Insertar la nueva categoría en la base de datos
    $result = $categories->insertOne([
        'name' => $data['name'],
        'description' => $data['description'],
        'parentId' => $data['parentId']
    ]);

    // Devolver la respuesta con el ID de la nueva categoría
    return createJsonResponse($response->withStatus(201), [
        'status' => 201,
        'message' => 'Category created',
        'categoryId' => (string) $result->getInsertedId()
    ]);
});

// Función para construir la jerarquía de categorías
function buildCategoryTree($categories, $parentId = null)
{
    $branch = [];
    foreach ($categories as $category) {
        if ($category['parentId'] == $parentId) {
            $children = buildCategoryTree($categories, (string) $category['_id']);
            if ($children) {
                $category['children'] = $children;
            }
            $branch[] = $category;
        }
    }
    return $branch;
}

// Endpoint para obtener la jerarquía completa de categorías
$app->get($prefix . '/categories/tree', function (RequestInterface $request, ResponseInterface $response) use ($categories) {
    $result = $categories->find()->toArray();

    // Convertir ObjectId a string
    $categoriesArray = array_map(function ($category) {
        $category['_id'] = (string) $category['_id'];
        $category['parentId'] = (string) $category['parentId'];
        return $category;
    }, $result);

    $categoryTree = buildCategoryTree($categoriesArray);

    return createJsonResponse($response, [
        'status' => 200,
        'data' => $categoryTree
    ]);
});

// Endpoint para obtener todas las categorías
$app->get($prefix . '/categories', function (RequestInterface $request, ResponseInterface $response) use ($categories) {
    $result = $categories->find()->toArray();

    // Convertir ObjectId a string
    $categoriesArray = array_map(function ($category) {
        $category['_id'] = (string) $category['_id'];
        return $category;
    }, $result);

    return createJsonResponse($response, [
        'status' => 200,
        'data' => $categoriesArray
    ]);
});

// Endpoint para obtener una categoría por ID
$app->get($prefix . '/categories/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($categories) {
    $id = $args['id'];

    // Validar el ID
    try {
        new ObjectId($id);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid category ID'
        ]);
    }

    // Buscar la categoría en la base de datos
    $category = $categories->findOne(['_id' => new ObjectId($id)]);

    if (!$category) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Category not found'
        ]);
    }

    // Convertir ObjectId a string
    $category['_id'] = (string) $category['_id'];

    return createJsonResponse($response, [
        'status' => 200,
        'data' => $category
    ]);
});

// Endpoint para actualizar una categoría por ID
$app->put($prefix . '/categories/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($categories) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    // Validar el ID
    try {
        new ObjectId($id);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid category ID'
        ]);
    }

    // Validar los datos de entrada
    if (!array_key_exists('name', $data) || !array_key_exists('description', $data) || !array_key_exists('parentId', $data)) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid input'
        ]);
    }

    // Actualizar la categoría en la base de datos
    $result = $categories->updateOne(
        ['_id' => new ObjectId($id)],
        ['$set' => [
            'name' => $data['name'],
            'description' => $data['description'],
            'parentId' => $data['parentId']
        ]]
    );

    if ($result->getMatchedCount() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Category not found'
        ]);
    }

    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'Category updated'
    ]);
});

// Endpoint para eliminar una categoría por ID
$app->delete($prefix . '/categories/{id}', function (RequestInterface $request, ResponseInterface $response, array $args) use ($categories) {
    $id = $args['id'];

    // Validar el ID
    try {
        new ObjectId($id);
    } catch (Exception $e) {
        return createJsonResponse($response->withStatus(400), [
            'status' => 400,
            'message' => 'Invalid category ID'
        ]);
    }

    // Eliminar la categoría de la base de datos
    $result = $categories->deleteOne(['_id' => new ObjectId($id)]);

    if ($result->getDeletedCount() === 0) {
        return createJsonResponse($response->withStatus(404), [
            'status' => 404,
            'message' => 'Category not found'
        ]);
    }

    return createJsonResponse($response, [
        'status' => 200,
        'message' => 'Category deleted'
    ]);
});
