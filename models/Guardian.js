const pool = require('../config/database');

class Guardian {
  static async create(guardianData) {
    const { user_id, guardian_id, relationship, occupation } = guardianData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'INSERT INTO guardians (user_id, guardian_id, relationship, occupation) VALUES (?, ?, ?, ?)',
        [user_id, guardian_id, relationship, occupation]
      );
      return result;
    } finally {
      connection.release();
    }
  }

  static async getAll() {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.execute(`
        SELECT g.*, u.name, u.email, u.phone, u.address, u.status 
        FROM guardians g 
        JOIN users u ON g.user_id = u.id
      `);
      return rows;
    } finally {
      connection.release();
    }
  }

  static async findById(id) {
    const connection = await pool.getConnection();
    try {
      const [rows] = await connection.execute(`
        SELECT g.*, u.name, u.email, u.phone, u.address, u.profile_image, u.status 
        FROM guardians g 
        JOIN users u ON g.user_id = u.id 
        WHERE g.id = ?
      `, [id]);
      return rows[0];
    } finally {
      connection.release();
    }
  }

  static async update(id, guardianData) {
    const { relationship, occupation } = guardianData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'UPDATE guardians SET relationship = ?, occupation = ? WHERE id = ?',
        [relationship, occupation, id]
      );
      return result;
    } finally {
      connection.release();
    }
  }

  static async delete(id) {
    const connection = await pool.getConnection();
    try {
      const [guardian] = await connection.execute('SELECT user_id FROM guardians WHERE id = ?', [id]);
      if (guardian.length > 0) {
        const user_id = guardian[0].user_id;
        await connection.execute('DELETE FROM guardians WHERE id = ?', [id]);
        await connection.execute('DELETE FROM users WHERE id = ?', [user_id]);
        return true;
      }
      return false;
    } finally {
      connection.release();
    }
  }
}

module.exports = Guardian;
