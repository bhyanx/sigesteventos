# Sistema de Gestión de Eventos Académicos

Sistema híbrido para la gestión de eventos académicos desarrollado en PHP y Node.js.

## Características

- Gestión completa de eventos académicos
- Registro y gestión de participantes
- Gestión de ponentes
- Sistema de inscripciones con control de cupos
- Seguimiento de pagos y asistencia
- Retroalimentación de participantes

## Tecnologías Utilizadas

- Backend: Node.js con Express
- Base de datos: MySQL
- Frontend: PHP (interfaz administrativa)
- API RESTful


## Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/tu-usuario/sigesteventos.git
   cd sigesteventos
   ```

2. **Configurar la base de datos**
   - Crear una base de datos MySQL
   - Importar el archivo `sistema_eventos_academicos.sql`
   
3. **Configurar la API**
   ```bash
   cd api
   cp .env.example .env
   # Editar .env con tus credenciales
   npm install
   ```

4. **Configurar el servidor web**
   - Configurar el documento root a la carpeta `php`
   - Asegurarse que PHP tenga permisos de escritura en las carpetas necesarias

## Estructura del Proyecto

```
.
├── api/                 # Backend Node.js
│   ├── controllers/    # Controladores de la API
│   ├── models/        # Modelos de datos
│   ├── routes/        # Rutas de la API
│   └── server.js      # Servidor principal
├── php/               # Frontend PHP
│   ├── config/       # Configuraciones
│   ├── views/        # Vistas
│   └── includes/     # Archivos incluidos
└── public/           # Archivos públicos
```

## Uso

1. **Iniciar la API**
   ```bash
   cd api
   npm start
   ```

2. **Acceder al sistema**
   - Abrir el navegador en `http://localhost:8080`

## Desarrollo

- Para desarrollo, usar `npm run dev` en la carpeta api
- Los cambios en PHP se reflejan inmediatamente
- Los cambios en la API requieren reiniciar el servidor

## Licencia

Este proyecto está bajo la Licencia MIT - ver el archivo [LICENSE.md](LICENSE.md) para detalles 
