# API REST para gestión de productos, categorías y valoraciones

Este proyecto es un despliegue de una API REST para gestionar productos, categorías y valoraciones almacenados en una base de datos MongoDB. La API REST permite crear, leer, actualizar y eliminar productos y categorías, así como añadir y leer valoraciones de los productos. Además, se pueden obtener los productos de una categoría y la jerarquía completa de categorías. La API REST está desarrollada con Slim y se despliega con Docker. El proyecto se compone de tres contenedores, uno para MongoDB, otro para Slim y otro para Nginx.

## Instalación

Basta con clonar el repositorio y ejecutar el siguiente comando:

```bash
docker-compose up -d
```

## Acceso a la API REST

La API REST se despliega en `http://localhost:8080`. A continuación se describen los endpoints disponibles:

### Endpoints

#### Productos

- `POST /api/products`: Crear un nuevo producto.
- `GET /api/products`: Obtener todos los productos. Parámetros opcionales de filtrado: `categoryId` y `name`
- `GET /api/products/{id}`: Obtener un producto por ID.
- `PUT /api/products/{id}`: Actualizar un producto por ID.
- `DELETE /api/products/{id}`: Eliminar un producto por ID. También se eliminan los comentarios asociados.

#### Categorías

- `POST /api/categories`: Crear una nueva categoría.
- `GET /api/categories`: Obtener todas las categorías.
- `GET /api/categories/tree`: Obtener la jerarquía completa de categorías.
- `GET /api/categories/{id}`: Obtener una categoría por ID.
- `PUT /api/categories/{id}`: Actualizar una categoría por ID.
- `DELETE /api/categories/{id}`: Eliminar una categoría por ID.

#### Comentarios

- `POST /api/reviews`: Añadir un comentario a un producto.
- `GET /api/reviews`: Obtener todos los comentarios. Parámetros opcionales de filtrado: `productId` y `userId`
- `PUT /api/reviews/{id}`: Actualizar un comentario (solo por el usuario que lo creó).
- `DELETE /api/reviews/{id}`: Eliminar un comentario por ID (solo por el usuario que lo creó).


### Ejemplo de JSON de un producto

```json
{
    "name": "Producto 1",
    "description": "Descripción del producto 1",
    "price": 100,
    "categoryId": "60c72b2f9b1d8b3a4c8b4567"
}
```

### Ejemplo de JSON de una categoría

```json
{
    "name": "Categoría 1",
    "description": "Descripción de la categoría 1",
    "parentId": "60c72b2f9b1d8b3a4c8b4567"
}
```

### Ejemplo de JSON de un comentario

```json
{
    "productId": "60c72b2f9b1d8b3a4c8b4567",
    "userId": "60c72b2f9b1d8b3a4c8b4567",
    "username": "usuario123",
    "rating": 4.5,
    "comment": "Muy buen producto, la batería dura bastante."
}
```

## Indices para rendimiento

Para mejorar el rendimiento de los endpoints, es recomendable definir los siguientes índices en MongoDB:

### Indices para la colección de productos

1. Indice en el campo `categoryId` para filtrar productos por categoría:
   ```bash
   db.products.createIndex({ categoryId: 1 })
   ```

2. Indice en el campo `name` para buscar productos por nombre:
   ```bash
   db.products.createIndex({ name: "text" })
   ```

### Indices para la colección de categorías

1. Indice en el campo `parentId` para construir la jerarquía de categorías:
   ```bash
   db.categories.createIndex({ parentId: 1 })
   ```

### Indices para la colección de comentarios

1. Indice en el campo `productId` para filtrar comentarios por producto:
   ```bash
   db.reviews.createIndex({ productId: 1 })
   ```

2. Indice en el campo `userId` para filtrar comentarios por usuario:
   ```bash
   db.reviews.createIndex({ userId: 1 })
   ```
