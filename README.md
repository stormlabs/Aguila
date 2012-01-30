Gestor de Proyectos Aguila
==========================

Aguila es un gestor simple de proyectos de software desarrollado en Symfony2.

Instalación
-----------

1. Obten el código

    git clone git://github.com/stormlabs/Aguila.git

2. Configuración
    Copia `/app/config/parameters.ini.dist` a `/app/config/parameters.ini` y
    editalo para utilizar los datos de tu base de datos.

3. Obten los vendors

    make vendors

4. Corre `make install` para inicializar la base de datos y añadir fixtures

    make install
