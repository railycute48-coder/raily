const pool = require('../config/database');

class Staff {
  static async create(staffData) {
    const { user_id, employee_id, department, position, qualification, hire_date, salary } = staffData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'INSERT INTO staff (user_id, employee_id, department, position, qualification, hire_date, salary) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [user_id, employee_id, department, position, qualification, hire_date, salary]
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
        SELECT s.*, u.name, u.email, u.phone, u.status 
        FROM staff s 
        JOIN users u ON s.user_id = u.id
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
        SELECT s.*, u.name, u.email, u.phone, u.address, u.profile_image, u.status 
        FROM staff s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.id = ?
      `, [id]);
      return rows[0];
    } finally {
      connection.release();
    }
  }

  static async update(id, staffData) {
    const { department, position, qualification, salary } = staffData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'UPDATE staff SET department = ?, position = ?, qualification = ?, salary = ? WHERE id = ?',
        [department, position, qualification, salary, id]
      );
      return result;
    } finally {
      connection.release();
    }
  }

  static async delete(id) {
    const connection = await pool.getConnection();
    try {
      // Get user_id first
      const [staff] = await connection.execute('SELECT user_id FROM staff WHERE id = ?', [id]);
      if (staff.length > 0) {
        const user_id = staff[0].user_id;
        await connection.execute('DELETE FROM staff WHERE id = ?', [id]);
        await connection.execute('DELETE FROM users WHERE id = ?', [user_id]);
        return true;
      }
      return false;
    } finally {
      connection.release();
    }
  }
}

module.exports = Staff;
