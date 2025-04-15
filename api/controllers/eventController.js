// api/controllers/eventController.js
const Event = require('../models/Event');

// Obtener todos los eventos
exports.getAllEvents = async (req, res) => {
  try {
    const events = await Event.findAll();
    res.status(200).json({ success: true, data: events });
  } catch (error) {
    res.status(500).json({ success: false, message: 'Error al obtener los eventos', error: error.message });
  }
};

// Obtener un evento por ID
exports.getEventById = async (req, res) => {
  try {
    const event = await Event.findById(req.params.id);
    if (!event) {
      return res.status(404).json({ success: false, message: 'Evento no encontrado' });
    }
    res.status(200).json({ success: true, data: event });
  } catch (error) {
    res.status(500).json({ success: false, message: 'Error al obtener el evento', error: error.message });
  }
};

// Crear un nuevo evento
exports.createEvent = async (req, res) => {
  try {
    const newEvent = await Event.create(req.body);
    res.status(201).json({ success: true, data: newEvent, message: 'Evento creado exitosamente' });
  } catch (error) {
    res.status(500).json({ success: false, message: 'Error al crear el evento', error: error.message });
  }
};

// Actualizar un evento
exports.updateEvent = async (req, res) => {
  try {
    const event = await Event.findById(req.params.id);
    if (!event) {
      return res.status(404).json({ success: false, message: 'Evento no encontrado' });
    }
   
    const updatedEvent = await Event.update(req.params.id, req.body);
    res.status(200).json({ success: true, data: updatedEvent, message: 'Evento actualizado exitosamente' });
  } catch (error) {
    res.status(500).json({ success: false, message: 'Error al actualizar el evento', error: error.message });
  }
};

// Eliminar un evento
exports.deleteEvent = async (req, res) => {
  try {
    const event = await Event.findById(req.params.id);
    if (!event) {
      return res.status(404).json({ success: false, message: 'Evento no encontrado' });
    }
   
    const deleted = await Event.delete(req.params.id);
    if (deleted) {
      res.status(200).json({ success: true, message: 'Evento eliminado exitosamente' });
    } else {
      res.status(400).json({ success: false, message: 'No se pudo eliminar el evento' });
    }
  } catch (error) {
    res.status(500).json({ success: false, message: 'Error al eliminar el evento', error: error.message });
  }
};
