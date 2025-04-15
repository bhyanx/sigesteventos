const express = require('express');
const router = express.Router();
const participantController = require('../controllers/participantController');

// Rutas CRUD para participantes
router.get('/', participantController.getAllParticipants);
router.get('/:id', participantController.getParticipantById);
router.post('/', participantController.createParticipant);
router.put('/:id', participantController.updateParticipant);
router.delete('/:id', participantController.deleteParticipant);

// Rutas adicionales
router.get('/:id/registrations', participantController.getParticipantRegistrations);
router.get('/:id/events', participantController.getParticipantEvents);

module.exports = router;
