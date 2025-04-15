const db = require('../config/db');


class Evento {
  static async findAll() {
    try {
      const [rows] = await db.query('SELECT * FROM eventos ORDER BY fecha_creacion DESC');
      return rows;
    } catch (error) {
      throw error;
    }
  }


  static async findById(id) {
    try {
      const [rows] = await db.query('SELECT * FROM eventos WHERE id_evento = ?', [id]);
      return rows[0];
    } catch (error) {
      throw error;
    }
  }


  static async create(evento) {
    try {
      const { titulo, id_categoria, descripcion, ubicacion, fecha_evento, hora_inicio, hora_fin, max_participantes, tarifa_inscripcion, imagen } = evento;
      const [result] = await db.query(
        'INSERT INTO eventos (titulo, id_categoria, descripcion, ubicacion, fecha_evento, hora_inicio, hora_fin, max_participantes, tarifa_inscripcion, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [titulo, id_categoria, descripcion, ubicacion, fecha_evento, hora_inicio, hora_fin, max_participantes, tarifa_inscripcion, imagen]
      );
      return { id_evento: result.insertId, ...evento };
    } catch (error) {
      throw error;
    }
  }


  static async update(id, evento) {
    try {
      const { titulo, id_categoria, descripcion, ubicacion, fecha_evento, hora_inicio, hora_fin, max_participantes, tarifa_inscripcion, imagen } = evento;
      await db.query(
        'UPDATE eventos SET titulo = ?, id_categoria = ?, descripcion = ?, ubicacion = ?, fecha_evento = ?, hora_inicio = ?, hora_fin = ?, max_participantes = ?, tarifa_inscripcion = ?, imagen = ? WHERE id_evento = ?',
        [titulo, id_categoria, descripcion, ubicacion, fecha_evento, hora_inicio, hora_fin, max_participantes, tarifa_inscripcion, imagen, id]
      );
      return { id_evento: id, ...evento };
    } catch (error) {
      throw error;
    }
  }


  static async delete(id) {
    try {
      const [result] = await db.query('DELETE FROM eventos WHERE id_evento = ?', [id]);
      return result.affectedRows > 0;
    } catch (error) {
      throw error;
    }
  }

  static async getEventosByCategoria(id_categoria) {
    try {
      const [rows] = await db.query('SELECT * FROM eventos WHERE id_categoria = ?', [id_categoria]);
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async getEventosProximos() {
    try {
      const [rows] = await db.query('SELECT * FROM eventos WHERE fecha_evento >= CURDATE() ORDER BY fecha_evento ASC');
      return rows;
    } catch (error) {
      throw error;
    }
  }

  static async getCuposDisponibles(id_evento) {
    try {
      const [evento] = await db.query('SELECT max_participantes FROM eventos WHERE id_evento = ?', [id_evento]);
      const [inscripciones] = await db.query('SELECT COUNT(*) as total FROM inscripciones WHERE id_evento = ?', [id_evento]);
      
      if (evento.length === 0) {
        throw new Error('Evento no encontrado');
      }
      
      return evento[0].max_participantes - inscripciones[0].total;
    } catch (error) {
      throw error;
    }
  }
}


module.exports = Evento;
