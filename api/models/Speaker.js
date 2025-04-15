const db = require('../config/db');

class Ponente {
  static async findAll() {
    try {
      const [rows] = await db.query('SELECT * FROM ponentes ORDER BY nombre, apellido');
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async findById(id) {
    try {
      const [rows] = await db.query('SELECT * FROM ponentes WHERE id_ponente = ?', [id]);
      return rows[0];
    } catch (error) {
      throw error;
    }
  }

  static async create(ponente) {
    try {
      const { nombre, apellido, email, telefono, especialidad, biografia } = ponente;
      const [result] = await db.query(
        'INSERT INTO ponentes (nombre, apellido, email, telefono, especialidad, biografia) VALUES (?, ?, ?, ?, ?, ?)',
        [nombre, apellido, email, telefono, especialidad, biografia]
      );
      return { id_ponente: result.insertId, ...ponente };
    } catch (error) {
      throw error;
    }
  }

  static async update(id, ponente) {
    try {
      const { nombre, apellido, email, telefono, especialidad, biografia } = ponente;
      await db.query(
        'UPDATE ponentes SET nombre = ?, apellido = ?, email = ?, telefono = ?, especialidad = ?, biografia = ? WHERE id_ponente = ?',
        [nombre, apellido, email, telefono, especialidad, biografia, id]
      );
      return { id_ponente: id, ...ponente };
    } catch (error) {
      throw error;
    }
  }

  static async delete(id) {
    try {
      const [result] = await db.query('DELETE FROM ponentes WHERE id_ponente = ?', [id]);
      return result.affectedRows > 0;
    } catch (error) {
      throw error;
    }
  }

  static async findByEmail(email) {
    try {
      const [rows] = await db.query('SELECT * FROM ponentes WHERE email = ?', [email]);
      return rows[0];
    } catch (error) {
      throw error;
    }
  }

  static async getEventos(id_ponente) {
    try {
      const [rows] = await db.query(
        `SELECT e.*, ep.titulo_presentacion, ep.hora_presentacion 
        FROM eventos e 
        JOIN eventos_ponentes ep ON e.id_evento = ep.id_evento 
        WHERE ep.id_ponente = ? 
        ORDER BY e.fecha_evento DESC`,
        [id_ponente]
      );
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async assignToEvent(id_ponente, id_evento, titulo_presentacion, hora_presentacion) {
    try {
      // Verificar si el evento existe
      const [evento] = await db.query('SELECT id_evento FROM eventos WHERE id_evento = ?', [id_evento]);
      if (evento.length === 0) {
        throw new Error('Evento no encontrado');
      }

      // Verificar si ya está asignado
      const [asignacionExistente] = await db.query(
        'SELECT id_evento FROM eventos_ponentes WHERE id_evento = ? AND id_ponente = ?',
        [id_evento, id_ponente]
      );

      if (asignacionExistente.length > 0) {
        throw new Error('El ponente ya está asignado a este evento');
      }

      // Asignar ponente al evento
      await db.query(
        'INSERT INTO eventos_ponentes (id_evento, id_ponente, titulo_presentacion, hora_presentacion) VALUES (?, ?, ?, ?)',
        [id_evento, id_ponente, titulo_presentacion, hora_presentacion]
      );

      return true;
    } catch (error) {
      throw error;
    }
  }

  static async removeFromEvent(id_ponente, id_evento) {
    try {
      const [result] = await db.query(
        'DELETE FROM eventos_ponentes WHERE id_evento = ? AND id_ponente = ?',
        [id_evento, id_ponente]
      );
      return result.affectedRows > 0;
    } catch (error) {
      throw error;
    }
  }

  static async getEstadisticas(id_ponente) {
    try {
      const [totalEventos] = await db.query(
        'SELECT COUNT(DISTINCT id_evento) as total FROM eventos_ponentes WHERE id_ponente = ?',
        [id_ponente]
      );
      
      const [eventosProximos] = await db.query(
        `SELECT COUNT(DISTINCT e.id_evento) as proximos 
        FROM eventos_ponentes ep 
        JOIN eventos e ON ep.id_evento = e.id_evento 
        WHERE ep.id_ponente = ? AND e.fecha_evento >= CURDATE()`,
        [id_ponente]
      );
      
      const [eventosPasados] = await db.query(
        `SELECT COUNT(DISTINCT e.id_evento) as pasados 
        FROM eventos_ponentes ep 
        JOIN eventos e ON ep.id_evento = e.id_evento 
        WHERE ep.id_ponente = ? AND e.fecha_evento < CURDATE()`,
        [id_ponente]
      );
      
      return {
        totalEventos: totalEventos[0].total,
        eventosProximos: eventosProximos[0].proximos,
        eventosPasados: eventosPasados[0].pasados
      };
    } catch (error) {
      throw error;
    }
  }
}

module.exports = Ponente;
