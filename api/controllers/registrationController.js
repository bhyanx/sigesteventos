const db = require('../config/db');

const registrationController = {
    // Obtener todas las inscripciones
    getAllRegistrations: async (req, res) => {
        try {
            const [registrations] = await db.query(
                `SELECT i.*, e.titulo as evento_titulo, p.nombre, p.apellido 
                FROM inscripciones i 
                JOIN eventos e ON i.id_evento = e.id_evento 
                JOIN participantes p ON i.id_participante = p.id_participante`
            );
            res.json(registrations);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Obtener una inscripción por ID
    getRegistrationById: async (req, res) => {
        try {
            const [registration] = await db.query(
                `SELECT i.*, e.titulo as evento_titulo, p.nombre, p.apellido 
                FROM inscripciones i 
                JOIN eventos e ON i.id_evento = e.id_evento 
                JOIN participantes p ON i.id_participante = p.id_participante 
                WHERE i.id_inscripcion = ?`,
                [req.params.id]
            );
            if (registration.length === 0) {
                return res.status(404).json({ message: 'Inscripción no encontrada' });
            }
            res.json(registration[0]);
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Crear una nueva inscripción
    createRegistration: async (req, res) => {
        try {
            const { id_evento, id_participante } = req.body;
            
            // Verificar cupos disponibles
            const [event] = await db.query(
                'SELECT max_participantes FROM eventos WHERE id_evento = ?',
                [id_evento]
            );
            
            const [currentRegistrations] = await db.query(
                'SELECT COUNT(*) as count FROM inscripciones WHERE id_evento = ?',
                [id_evento]
            );

            if (currentRegistrations[0].count >= event[0].max_participantes) {
                return res.status(400).json({ message: 'No hay cupos disponibles para este evento' });
            }

            // Crear la inscripción
            const [result] = await db.query(
                'INSERT INTO inscripciones (id_evento, id_participante) VALUES (?, ?)',
                [id_evento, id_participante]
            );
            
            res.status(201).json({
                id: result.insertId,
                message: 'Inscripción creada exitosamente'
            });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Actualizar una inscripción
    updateRegistration: async (req, res) => {
        try {
            const { estado_pago, asistencia } = req.body;
            const [result] = await db.query(
                'UPDATE inscripciones SET estado_pago = ?, asistencia = ? WHERE id_inscripcion = ?',
                [estado_pago, asistencia, req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Inscripción no encontrada' });
            }
            res.json({ message: 'Inscripción actualizada exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Eliminar una inscripción
    deleteRegistration: async (req, res) => {
        try {
            const [result] = await db.query(
                'DELETE FROM inscripciones WHERE id_inscripcion = ?',
                [req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Inscripción no encontrada' });
            }
            res.json({ message: 'Inscripción eliminada exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Actualizar estado de pago
    updatePaymentStatus: async (req, res) => {
        try {
            const { estado_pago } = req.body;
            const [result] = await db.query(
                'UPDATE inscripciones SET estado_pago = ? WHERE id_inscripcion = ?',
                [estado_pago, req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Inscripción no encontrada' });
            }
            res.json({ message: 'Estado de pago actualizado exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Actualizar asistencia
    updateAttendance: async (req, res) => {
        try {
            const { asistencia } = req.body;
            const [result] = await db.query(
                'UPDATE inscripciones SET asistencia = ? WHERE id_inscripcion = ?',
                [asistencia, req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Inscripción no encontrada' });
            }
            res.json({ message: 'Asistencia actualizada exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    },

    // Agregar retroalimentación
    addFeedback: async (req, res) => {
        try {
            const { retroalimentacion } = req.body;
            const [result] = await db.query(
                'UPDATE inscripciones SET retroalimentacion = ? WHERE id_inscripcion = ?',
                [retroalimentacion, req.params.id]
            );
            if (result.affectedRows === 0) {
                return res.status(404).json({ message: 'Inscripción no encontrada' });
            }
            res.json({ message: 'Retroalimentación agregada exitosamente' });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    }
};

module.exports = registrationController;
