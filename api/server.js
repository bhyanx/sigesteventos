// api/server.js
require('dotenv').config();
const express = require('express');
const cors = require('cors');
const eventoRoutes = require('./routes/eventRoutes');
const participantRoutes = require('./routes/participantRoutes');
const registrationRoutes = require('./routes/registrationRoutes');
const speakerRoutes = require('./routes/speakerRoutes');

// Inicializar Express
const app = express();
const PORT = process.env.PORT || 3000;

// Configuración de CORS
const corsOptions = {
    origin: process.env.CORS_ORIGIN || 'http://localhost:8080',
    optionsSuccessStatus: 200
};

// Middleware
app.use(cors(corsOptions));
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Rutas
app.use('/api/eventos', eventoRoutes);
app.use('/api/participantes', participantRoutes);
app.use('/api/inscripciones', registrationRoutes);
app.use('/api/ponentes', speakerRoutes);

// Ruta de prueba
app.get('/', (req, res) => {
    res.json({ message: 'Bienvenido a la API de Gestion de Eventos' });
});

// Manejo de errores
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).json({
        error: 'Error interno del servidor',
        message: err.message
    });
});

// Iniciar servidor
app.listen(PORT, () => {
    console.log(`Servidor corriendo en http://localhost:${PORT}`);
});
