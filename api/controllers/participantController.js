const db = require('../config/db');

const participantController = {
    // Obtener todos los participantes
    getAllParticipants: async (req, res) => {
        try {
            const [participants] = await db.query('SELECT * FROM participantes');
            res.json(participants);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Obtener un participante por ID
    getParticipantById: async (req, res) => {
        try {
            const [participant] = await db.query(
                'SELECT * FROM participantes WHERE id_participante = ?',
                [req.params.id]
            );
            if (participant.length === 0) {
                return res.status(404).json({ message: 'Participante no encontrado' });
            }
            res.json(participant[0]);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Crear un nuevo participante
    createParticipant: async (req, res) => {
        try {
            const { nombre, apellido, email, telefono, institucion } = req.body;
            const [result] = await db.query(
                'INSERT INTO participantes (nombre, apellido, email, telefono, institucion) VALUES (?, ?, ?, ?, ?)',
                [nombre, apellido, email, telefono, institucion]
            );
            res.status(201).json({
                id: result.insertId,
                message: 'Participante creado exitosamente'
            });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Actualizar un participante
    updateParticipant: async (req, res) => {
        try {
            const { nombre, apellido, email, telefono, institucion } = req.body;
            const [result] = await db.query(
                'UPDATE participantes SET nombre = ?, apellido = ?, email = ?, telefono = ?, institucion = ? WHERE id_participante = ?',
                [nombre, apellido, email, telefono, institucion, req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Participante no encontrado' });
            }
            res.json({ message: 'Participante actualizado exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Eliminar un participante
    deleteParticipant: async (req, res) => {
        try {
            const [result] = await db.query(
                'DELETE FROM participantes WHERE id_participante = ?',
                [req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Participante no encontrado' });
            }
            res.json({ message: 'Participante eliminado exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Obtener inscripciones de un participante
    getParticipantRegistrations: async (req, res) => {
        try {
            const [registrations] = await db.query(
                `SELECT i.*, e.titulo as evento_titulo 
                FROM inscripciones i 
                JOIN eventos e ON i.id_evento = e.id_evento 
                WHERE i.id_participante = ?`,
                [req.params.id]
            );
            res.json(registrations);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Obtener eventos de un participante
    getParticipantEvents: async (req, res) => {
        try {
            const [events] = await db.query(
                `SELECT e.* 
                FROM eventos e 
                JOIN inscripciones i ON e.id_evento = i.id_evento 
                WHERE i.id_participante = ?`,
                [req.params.id]
            );
            res.json(events);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    }
};

module.exports = participantController;
