const pool = require('../config/database');

class Course {
  static async create(courseData) {
    const { course_name, course_code, description, credits, class_id, staff_id, duration_hours } = courseData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'INSERT INTO courses (course_name, course_code, description, credits, class_id, staff_id, duration_hours) VALUES (?, ?, ?, ?, ?, ?, ?)',
        [course_name, course_code, description, credits, class_id, staff_id, duration_hours]
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
        SELECT c.*, cl.class_name, u.name as staff_name 
        FROM courses c 
        JOIN classes cl ON c.class_id = cl.id 
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
        SELECT c.*, cl.class_name, u.name as staff_name 
        FROM courses c 
        JOIN classes cl ON c.class_id = cl.id 
        LEFT JOIN staff s ON c.staff_id = s.id 
        LEFT JOIN users u ON s.user_id = u.id 
        WHERE c.id = ?
      `, [id]);
      return rows[0];
    } finally {
      connection.release();
    }
  }

  static async update(id, courseData) {
    const { course_name, description, credits, staff_id } = courseData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'UPDATE courses SET course_name = ?, description = ?, credits = ?, staff_id = ? WHERE id = ?',
        [course_name, description, credits, staff_id, id]
      );
      return result;
    } finally {
      connection.release();
    }
  }

  static async delete(id) {
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute('DELETE FROM courses WHERE id = ?', [id]);
      return result;
    } finally {
      connection.release();
    }
  }
}

module.exports = Course;
