# inscripcion-uls
Modulo de inscripción para la Universidad Luterana Salvadoreña. Crea recibos con NPE, crea horarios según carga academica.

Instalación
-----------
El sistema utiliza composer para la instalación de dependencias.
Puedes descargar composer desde [aquí](https://getcomposer.org/download/)

Una vez instalado composer, en la terminal ubicate en la carpeta del proyecto y corre el siguiente comando:

```
php composer.phar install
```

Requisitos
----------
El sistema requiere de los modulos de PHP:

- GD: para la creación de imágenes de los códigos de barra.
- mbstring: para manejar carácteres como la ñ y tildes.

Si el sistema no muestra los recibos de pagos, posiblemente se necesite cambiar los permisos de la carpeta img/barcodes.

```
chmod -R 777 img/barcodes
```
