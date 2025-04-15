// api/config/db.js
const mysql = require('mysql2/promise');

// Configuración de la base de datos
const dbConfig = {
    host: process.env.DB_HOST || 'localhost',
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'sistema_eventos_academicos',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
};

// Crear pool de conexiones
const pool = mysql.createPool(dbConfig);

// Verificar conexión
pool.getConnection()
    .then(connection => {
        console.log('Base de datos conectada exitosamente');
        connection.release();
    })
    .catch(err => {
        console.error('Error al conectar con la base de datos:', err);
    });

module.exports = pool;
