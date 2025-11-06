require('dotenv').config();
const mysql = require('mysql');
const util = require('util');

const pool = mysql.createPool({
  connectionLimit: process.env.DB_CONNECTION_LIMIT || 10,
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME || 'listado'
});

pool.query = util.promisify(pool.query);
module.exports = pool;