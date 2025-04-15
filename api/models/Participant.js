const db = require('../config/db');

class Participante {
  static async findAll() {
    try {
      const [rows] = await db.query('SELECT * FROM participantes ORDER BY nombre, apellido');
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async findById(id) {
    try {
      const [rows] = await db.query('SELECT * FROM participantes WHERE id_participante = ?', [id]);
      return rows[0];
    } catch (error) {
      throw error;
    }
  }

  static async create(participante) {
    try {
      const { nombre, apellido, email, telefono, institucion } = participante;
      const [result] = await db.query(
        'INSERT INTO participantes (nombre, apellido, email, telefono, institucion) VALUES (?, ?, ?, ?, ?)',
        [nombre, apellido, email, telefono, institucion]
      );
      return { id_participante: result.insertId, ...participante };
    } catch (error) {
      throw error;
    }
  }

  static async update(id, participante) {
    try {
      const { nombre, apellido, email, telefono, institucion } = participante;
      await db.query(
        'UPDATE participantes SET nombre = ?, apellido = ?, email = ?, telefono = ?, institucion = ? WHERE id_participante = ?',
        [nombre, apellido, email, telefono, institucion, id]
      );
      return { id_participante: id, ...participante };
    } catch (error) {
      throw error;
    }
  }

  static async delete(id) {
    try {
      const [result] = await db.query('DELETE FROM participantes WHERE id_participante = ?', [id]);
      return result.affectedRows > 0;
    } catch (error) {
      throw error;
    }
  }

  static async findByEmail(email) {
    try {
      const [rows] = await db.query('SELECT * FROM participantes WHERE email = ?', [email]);
      return rows[0];
    } catch (error) {
      throw error;
    }
  }

  static async getInscripciones(id_participante) {
    try {
      const [rows] = await db.query(
        `SELECT i.*, e.titulo as evento_titulo, e.fecha_evento, e.hora_inicio, e.hora_fin 
        FROM inscripciones i 
        JOIN eventos e ON i.id_evento = e.id_evento 
        WHERE i.id_participante = ? 
        ORDER BY e.fecha_evento DESC`,
        [id_participante]
      );
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async getEventos(id_participante) {
    try {
      const [rows] = await db.query(
        `SELECT e.* 
        FROM eventos e 
        JOIN inscripciones i ON e.id_evento = i.id_evento 
        WHERE i.id_participante = ? 
        ORDER BY e.fecha_evento DESC`,
        [id_participante]
      );
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async getEstadisticas(id_participante) {
    try {
      const [totalInscripciones] = await db.query(
        'SELECT COUNT(*) as total FROM inscripciones WHERE id_participante = ?',
        [id_participante]
      );
      
      const [asistencias] = await db.query(
        'SELECT COUNT(*) as asistencias FROM inscripciones WHERE id_participante = ? AND asistencia = 1',
        [id_participante]
      );
      
      const [pagados] = await db.query(
        'SELECT COUNT(*) as pagados FROM inscripciones WHERE id_participante = ? AND estado_pago = "pagado"',
        [id_participante]
      );
      
      return {
        totalInscripciones: totalInscripciones[0].total,
        asistencias: asistencias[0].asistencias,
        pagados: pagados[0].pagados
      };
    } catch (error) {
      throw error;
    }
  }
}

module.exports = Participante;
