const pool = require('../config/database');
const bcrypt = require('bcryptjs');

class User {
  static async create(userData) {
    const { name, email, password, phone, address, role } = userData;
    const hashedPassword = await bcrypt.hash(password, 10);

    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, ?)',
        [name, email, hashedPassword, phone, address, role]
      );
      return result;
    } finally {
      connection.release();
    }
  }

  static async findByEmail(email) {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.execute('SELECT * FROM users WHERE email = ?', [email]);
      return rows[0];
    } finally {
      connection.release();
    }
  }

  static async findById(id) {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.execute('SELECT id, name, email, phone, address, role, profile_image, status FROM users WHERE id = ?', [id]);
      return rows[0];
    } finally {
      connection.release();
    }
  }

  static async getAll(role = null) {
    const connection = await pool.getConnection();
    try {
      let query = 'SELECT id, name, email, phone, role, status, created_at FROM users';
      if (role) {
        query += ' WHERE role = ?';
        const [rows] = await connection.execute(query, [role]);
        return rows;
      }
      const [rows] = await connection.execute(query);
      return rows;
    } finally {
      connection.release();
    }
  }

  static async update(id, userData) {
    const connection = await pool.getConnection();
    try {
      const { name, email, phone, address, profile_image } = userData;
      const [result] = await connection.execute(
        'UPDATE users SET name = ?, email = ?, phone = ?, address = ?, profile_image = ? WHERE id = ?',
        [name, email, phone, address, profile_image, id]
      );
      return result;
    } finally {
      connection.release();
    }
  }

  static async updateStatus(id, status) {
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute('UPDATE users SET status = ? WHERE id = ?', [status, id]);
      return result;
    } finally {
      connection.release();
    }
  }

  static async verifyPassword(password, hashedPassword) {
    return await bcrypt.compare(password, hashedPassword);
  }
}

module.exports = User;
