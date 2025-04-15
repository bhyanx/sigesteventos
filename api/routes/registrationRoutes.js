const express = require('express');
const router = express.Router();
const registrationController = require('../controllers/registrationController');

// Rutas CRUD para inscripciones
router.get('/', registrationController.getAllRegistrations);
router.get('/:id', registrationController.getRegistrationById);
router.post('/', registrationController.createRegistration);
router.put('/:id', registrationController.updateRegistration);
router.delete('/:id', registrationController.deleteRegistration);

// Rutas adicionales
router.put('/:id/payment', registrationController.updatePaymentStatus);
router.put('/:id/attendance', registrationController.updateAttendance);
router.post('/:id/feedback', registrationController.addFeedback);

module.exports = router;
