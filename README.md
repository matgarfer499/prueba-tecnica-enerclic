# PRUEBA TÉCNICA ENERCLIC

Para la realización de esta prueba he usado el framework Laravel

## Instrucciones de instalación y ejecución

1. Clonar el repositorio

2. Acceder a la ruta donde se ha clonado el repositorio

3. Instalar las dependencias de composer y npm:
```
    - composer install
    - npm install
```

4. Cambiar el nombre del archivo ".env.example" por ".env"

5. Dentro de este archivo cambiar estos datos por los necesarios:
```
    DB_CONNECTION=mysql
    DB_HOST= (HOST)
    DB_PORT= (PUERTO)
    DB_DATABASE= (NOMBRE BASE DE DATOS)
    DB_USERNAME= (USUARIO)
    DB_PASSWORD= (CONTRASEÑA)
```

6. Generar la clave de la aplicación:
```
    - php artisan key:generate
```

7. Crear la tabla:
```
    - php artisan migrate
```

8. Ejecutar los assets con Laravel Mix:
```
    - npm run dev
```

9. En otra terminal ejecutar el servidor:
```
    - php artisan serve
```
