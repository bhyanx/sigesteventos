const express = require('express');
const router = express.Router();
const userController = require('../controllers/userController');
// const auth = require('../middleware/auth');

// Rutas públicas
router.post('/', userController.createUser);
router.get('/', userController.getAllUsers);
router.get('/:id', userController.getUserById);
router.put('/:id', userController.updateUser);
router.delete('/:id', userController.deleteUser);

module.exports = router; 