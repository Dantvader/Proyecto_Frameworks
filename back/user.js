const express = require('express');
const router = express.Router();
const db = require('../database');
const jwt = require('jsonwebtoken');

// POST /user/login
router.post('/login', async (req, res) => {
  const { user_mail, user_password } = req.body;

  if (!user_mail || !user_password) {
    return res.status(400).json({ code: 400, message: 'Campos incompletos' });
  }

  const query = 'SELECT * FROM user WHERE user_mail = ? AND user_password = ?';
  try {
    const rows = await db.query(query, [user_mail, user_password]);

    if (rows.length === 1) {
      const token = jwt.sign({
        user_id: rows[0].user_id,
        user_mail: rows[0].user_mail
      }, 'debugkey');

      return res.status(200).json({ code: 200, message: token });
    }

    return res.status(401).json({ code: 401, message: 'Usuario y/o contraseña incorrectos' });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ code: 500, message: 'Error interno' });
  }
});

// GET /user/
router.get('/', async (req, res) => {
  try {
    const rows = await db.query('SELECT * FROM user');
    return res.status(200).json({ code: 200, message: rows });
  } catch (err) {
    console.error(err);
    return res.status(500).json({ code: 500, message: 'Error interno' });
  }
});

module.exports = router;