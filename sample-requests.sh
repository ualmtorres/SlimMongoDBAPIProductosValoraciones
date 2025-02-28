!# /bin/bash
# Create main categories

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Electrónica",
  "description": "Dispositivos electrónicos",
  "parentId": null
}'

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Hogar",
  "description": "Artículos para el hogar",
  "parentId": null
}'

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Ropa",
  "description": "Prendas de vestir",
  "parentId": null
}'

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Deportes",
  "description": "Artículos deportivos",
  "parentId": null
}'

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Libros",
  "description": "Libros y revistas",
  "parentId": null
}'

# Create subcategories

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Móviles",
  "description": "Teléfonos móviles y accesorios",
  "parentId": "ID_DE_ELECTRONICA"
}'

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Televisores",
  "description": "Televisores y accesorios",
  "parentId": "ID_DE_ELECTRONICA"
}'

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Muebles",
  "description": "Muebles para el hogar",
  "parentId": "ID_DE_HOGAR"
}'

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Cocina",
  "description": "Utensilios de cocina",
  "parentId": "ID_DE_HOGAR"
}'

curl -X POST http://localhost:8084/api/categories -H "Content-Type: application/json" -d '{
  "name": "Calzado",
  "description": "Zapatos y sandalias",
  "parentId": "ID_DE_ROPA"
}'

# Create products

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Smartphone",
  "description": "Un smartphone de última generación",
  "price": 699,
  "categoryId": "ID_DE_ELECTRONICA"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Laptop",
  "description": "Una laptop potente para trabajo y juegos",
  "price": 1299,
  "categoryId": "ID_DE_ELECTRONICA"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Sofá",
  "description": "Un sofá cómodo para tu sala de estar",
  "price": 499,
  "categoryId": "ID_DE_HOGAR"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Mesa de comedor",
  "description": "Una mesa de comedor elegante",
  "price": 299,
  "categoryId": "ID_DE_HOGAR"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Camiseta",
  "description": "Una camiseta de algodón",
  "price": 19,
  "categoryId": "ID_DE_ROPA"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Pantalones",
  "description": "Pantalones cómodos y elegantes",
  "price": 39,
  "categoryId": "ID_DE_ROPA"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Bicicleta",
  "description": "Una bicicleta de montaña",
  "price": 599,
  "categoryId": "ID_DE_DEPORTES"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Pelota de fútbol",
  "description": "Una pelota de fútbol profesional",
  "price": 29,
  "categoryId": "ID_DE_DEPORTES"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Novela",
  "description": "Una novela de misterio",
  "price": 15,
  "categoryId": "ID_DE_LIBROS"
}'

curl -X POST http://localhost:8084/api/products -H "Content-Type: application/json" -d '{
  "name": "Revista",
  "description": "Una revista de moda",
  "price": 5,
  "categoryId": "ID_DE_LIBROS"
}'

## Create reviews

curl -X POST http://localhost:8084/api/reviews -H "Content-Type: application/json" -d '{
  "productId": "67c0c47ede5167bcbc09eb65",
  "userId": "60c72b2f9b1d8b3a4c8b4567",
  "username": "Usuario1",
  "rating": 5,
  "comment": "Excelente producto, muy recomendado."
}'

curl -X POST http://localhost:8084/api/reviews -H "Content-Type: application/json" -d '{
  "productId": "67c0c47ede5167bcbc09eb65",
  "userId": "60c72b2f9b1d8b3a4c8b4568",
  "username": "Usuario2",
  "rating": 4,
  "comment": "Muy buen producto, aunque podría mejorar."
}'


curl -X POST http://localhost:8084/api/reviews -H "Content-Type: application/json" -d '{
  "productId": "67c0c47e953f04997706efb1",
  "userId": "60c72b2f9b1d8b3a4c8b4567",
  "username": "Usuario1",
  "rating": 2,
  "comment": "No estoy muy contento con este producto."
}'
