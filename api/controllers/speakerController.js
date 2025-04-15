const db = require('../config/db');

const speakerController = {
    // Obtener todos los ponentes
    getAllSpeakers: async (req, res) => {
        try {
            const [speakers] = await db.query('SELECT * FROM ponentes');
            res.json(speakers);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Obtener un ponente por ID
    getSpeakerById: async (req, res) => {
        try {
            const [speaker] = await db.query(
                'SELECT * FROM ponentes WHERE id_ponente = ?',
                [req.params.id]
            );
            if (speaker.length === 0) {
                return res.status(404).json({ message: 'Ponente no encontrado' });
            }
            res.json(speaker[0]);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Crear un nuevo ponente
    createSpeaker: async (req, res) => {
        try {
            const { nombre, apellido, email, telefono, especialidad, biografia } = req.body;
            const [result] = await db.query(
                'INSERT INTO ponentes (nombre, apellido, email, telefono, especialidad, biografia) VALUES (?, ?, ?, ?, ?, ?)',
                [nombre, apellido, email, telefono, especialidad, biografia]
            );
            res.status(201).json({
                id: result.insertId,
                message: 'Ponente creado exitosamente'
            });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Actualizar un ponente
    updateSpeaker: async (req, res) => {
        try {
            const { nombre, apellido, email, telefono, especialidad, biografia } = req.body;
            const [result] = await db.query(
                'UPDATE ponentes SET nombre = ?, apellido = ?, email = ?, telefono = ?, especialidad = ?, biografia = ? WHERE id_ponente = ?',
                [nombre, apellido, email, telefono, especialidad, biografia, req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Ponente no encontrado' });
            }
            res.json({ message: 'Ponente actualizado exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Eliminar un ponente
    deleteSpeaker: async (req, res) => {
        try {
            const [result] = await db.query(
                'DELETE FROM ponentes WHERE id_ponente = ?',
                [req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Ponente no encontrado' });
            }
            res.json({ message: 'Ponente eliminado exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Obtener eventos de un ponente
    getSpeakerEvents: async (req, res) => {
        try {
            const [events] = await db.query(
                `SELECT e.*, ep.titulo_presentacion, ep.hora_presentacion 
                FROM eventos e 
                JOIN eventos_ponentes ep ON e.id_evento = ep.id_evento 
                WHERE ep.id_ponente = ?`,
                [req.params.id]
            );
            res.json(events);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Asignar ponente a un evento
    assignSpeakerToEvent: async (req, res) => {
        try {
            const { titulo_presentacion, hora_presentacion } = req.body;
            const id_evento = req.params.eventId;
            const id_ponente = req.params.id;

            // Verificar si el evento existe
            const [event] = await db.query(
                'SELECT id_evento FROM eventos WHERE id_evento = ?',
                [id_evento]
            );
            if (event.length === 0) {
                return res.status(404).json({ message: 'Evento no encontrado' });
            }

            // Asignar ponente al evento
            await db.query(
                'INSERT INTO eventos_ponentes (id_evento, id_ponente, titulo_presentacion, hora_presentacion) VALUES (?, ?, ?, ?)',
                [id_evento, id_ponente, titulo_presentacion, hora_presentacion]
            );

            res.json({ message: 'Ponente asignado al evento exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Remover ponente de un evento
    removeSpeakerFromEvent: async (req, res) => {
        try {
            const id_evento = req.params.eventId;
            const id_ponente = req.params.id;

            const [result] = await db.query(
                'DELETE FROM eventos_ponentes WHERE id_evento = ? AND id_ponente = ?',
                [id_evento, id_ponente]
            );

            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Asignación no encontrada' });
            }

            res.json({ message: 'Ponente removido del evento exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    }
};

module.exports = speakerController;
