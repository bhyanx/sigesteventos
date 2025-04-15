const express = require('express');
const router = express.Router();
const speakerController = require('../controllers/speakerController');

// Rutas CRUD para ponentes
router.get('/', speakerController.getAllSpeakers);
router.get('/:id', speakerController.getSpeakerById);
router.post('/', speakerController.createSpeaker);
router.put('/:id', speakerController.updateSpeaker);
router.delete('/:id', speakerController.deleteSpeaker);

// Rutas adicionales
router.get('/:id/events', speakerController.getSpeakerEvents);
router.post('/:id/events/:eventId', speakerController.assignSpeakerToEvent);
router.delete('/:id/events/:eventId', speakerController.removeSpeakerFromEvent);

module.exports = router;
