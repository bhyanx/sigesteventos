const db = require('../config/db');

class Inscripcion {
  static async findAll() {
    try {
      const [rows] = await db.query(
        `SELECT i.*, e.titulo as evento_titulo, p.nombre, p.apellido 
        FROM inscripciones i 
        JOIN eventos e ON i.id_evento = e.id_evento 
        JOIN participantes p ON i.id_participante = p.id_participante 
        ORDER BY i.fecha_inscripcion DESC`
      );
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async findById(id) {
    try {
      const [rows] = await db.query(
        `SELECT i.*, e.titulo as evento_titulo, p.nombre, p.apellido 
        FROM inscripciones i 
        JOIN eventos e ON i.id_evento = e.id_evento 
        JOIN participantes p ON i.id_participante = p.id_participante 
        WHERE i.id_inscripcion = ?`,
        [id]
      );
      return rows[0];
    } catch (error) {
      throw error;
    }
  }

  static async create(inscripcion) {
    try {
      const { id_evento, id_participante } = inscripcion;
      
      // Verificar cupos disponibles
      const [evento] = await db.query(
        'SELECT max_participantes FROM eventos WHERE id_evento = ?',
        [id_evento]
      );
      
      const [inscripciones] = await db.query(
        'SELECT COUNT(*) as total FROM inscripciones WHERE id_evento = ?',
        [id_evento]
      );

      if (inscripciones[0].total >= evento[0].max_participantes) {
        throw new Error('No hay cupos disponibles para este evento');
      }

      // Verificar si ya está inscrito
      const [inscripcionExistente] = await db.query(
        'SELECT id_inscripcion FROM inscripciones WHERE id_evento = ? AND id_participante = ?',
        [id_evento, id_participante]
      );

      if (inscripcionExistente.length > 0) {
        throw new Error('El participante ya está inscrito en este evento');
      }

      // Crear la inscripción
      const [result] = await db.query(
        'INSERT INTO inscripciones (id_evento, id_participante) VALUES (?, ?)',
        [id_evento, id_participante]
      );
      
      return { id_inscripcion: result.insertId, ...inscripcion };
    } catch (error) {
      throw error;
    }
  }

  static async update(id, inscripcion) {
    try {
      const { estado_pago, asistencia } = inscripcion;
      await db.query(
        'UPDATE inscripciones SET estado_pago = ?, asistencia = ? WHERE id_inscripcion = ?',
        [estado_pago, asistencia, id]
      );
      return { id_inscripcion: id, ...inscripcion };
    } catch (error) {
      throw error;
    }
  }

  static async delete(id) {
    try {
      const [result] = await db.query('DELETE FROM inscripciones WHERE id_inscripcion = ?', [id]);
      return result.affectedRows > 0;
    } catch (error) {
      throw error;
    }
  }

  static async updatePaymentStatus(id, estado_pago) {
    try {
      const [result] = await db.query(
        'UPDATE inscripciones SET estado_pago = ? WHERE id_inscripcion = ?',
        [estado_pago, id]
      );
      return result.affectedRows > 0;
    } catch (error) {
      throw error;
    }
  }

  static async updateAttendance(id, asistencia) {
    try {
      const [result] = await db.query(
        'UPDATE inscripciones SET asistencia = ? WHERE id_inscripcion = ?',
        [asistencia, id]
      );
      return result.affectedRows > 0;
    } catch (error) {
      throw error;
    }
  }

  static async addFeedback(id, retroalimentacion) {
    try {
      const [result] = await db.query(
        'UPDATE inscripciones SET retroalimentacion = ? WHERE id_inscripcion = ?',
        [retroalimentacion, id]
      );
      return result.affectedRows > 0;
    } catch (error) {
      throw error;
    }
  }

  static async getByEvento(id_evento) {
    try {
      const [rows] = await db.query(
        `SELECT i.*, p.nombre, p.apellido, p.email 
        FROM inscripciones i 
        JOIN participantes p ON i.id_participante = p.id_participante 
        WHERE i.id_evento = ? 
        ORDER BY i.fecha_inscripcion DESC`,
        [id_evento]
      );
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async getEstadisticasEvento(id_evento) {
    try {
      const [total] = await db.query(
        'SELECT COUNT(*) as total FROM inscripciones WHERE id_evento = ?',
        [id_evento]
      );
      
      const [asistencias] = await db.query(
        'SELECT COUNT(*) as asistencias FROM inscripciones WHERE id_evento = ? AND asistencia = 1',
        [id_evento]
      );
      
      const [pagados] = await db.query(
        'SELECT COUNT(*) as pagados FROM inscripciones WHERE id_evento = ? AND estado_pago = "pagado"',
        [id_evento]
      );
      
      return {
        totalInscripciones: total[0].total,
        asistencias: asistencias[0].asistencias,
        pagados: pagados[0].pagados
      };
    } catch (error) {
      throw error;
    }
  }
}

module.exports = Inscripcion;
