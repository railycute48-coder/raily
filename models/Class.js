const pool = require('../config/database');

class Class {
  static async create(classData) {
    const { class_name, class_code, description, capacity, staff_id, academic_year } = classData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'INSERT INTO classes (class_name, class_code, description, capacity, staff_id, academic_year) VALUES (?, ?, ?, ?, ?, ?)',
        [class_name, class_code, description, capacity, staff_id, academic_year]
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
        SELECT c.*, s.position, u.name as staff_name 
        FROM classes c 
        LEFT JOIN staff s ON c.staff_id = s.id 
        LEFT JOIN users u ON s.user_id = u.id
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
        SELECT c.*, u.name as staff_name, u.email as staff_email 
        FROM classes c 
        LEFT JOIN staff s ON c.staff_id = s.id 
        LEFT JOIN users u ON s.user_id = u.id 
        WHERE c.id = ?
      `, [id]);
      return rows[0];
    } finally {
      connection.release();
    }
  }

  static async update(id, classData) {
    const { class_name, description, capacity, staff_id } = classData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'UPDATE classes SET class_name = ?, description = ?, capacity = ?, staff_id = ? WHERE id = ?',
        [class_name, description, capacity, staff_id, id]
      );
      return result;
    } finally {
      connection.release();
    }
  }

  static async delete(id) {
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute('DELETE FROM classes WHERE id = ?', [id]);
      return result;
    } finally {
      connection.release();
    }
  }
}

module.exports = Class;
