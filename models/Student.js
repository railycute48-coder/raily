const pool = require('../config/database');

class Student {
  static async create(studentData) {
    const { user_id, student_id, registration_number, date_of_birth, gender, blood_group, admission_date, class_id, guardian_id } = studentData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'INSERT INTO students (user_id, student_id, registration_number, date_of_birth, gender, blood_group, admission_date, class_id, guardian_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
        [user_id, student_id, registration_number, date_of_birth, gender, blood_group, admission_date, class_id, guardian_id]
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
        SELECT s.*, u.name, u.email, u.phone, u.status, c.class_name 
        FROM students s 
        JOIN users u ON s.user_id = u.id 
        LEFT JOIN classes c ON s.class_id = c.id
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
        SELECT s.*, u.name, u.email, u.phone, u.address, u.profile_image, u.status, c.class_name 
        FROM students s 
        JOIN users u ON s.user_id = u.id 
        LEFT JOIN classes c ON s.class_id = c.id 
        WHERE s.id = ?
      `, [id]);
      return rows[0];
    } finally {
      connection.release();
    }
  }

  static async update(id, studentData) {
    const { class_id, guardian_id } = studentData;
    const connection = await pool.getConnection();
    try {
      const [result] = await connection.execute(
        'UPDATE students SET class_id = ?, guardian_id = ? WHERE id = ?',
        [class_id, guardian_id, id]
      );
      return result;
    } finally {
      connection.release();
    }
  }

  static async delete(id) {
    const connection = await pool.getConnection();
    try {
      const [student] = await connection.execute('SELECT user_id FROM students WHERE id = ?', [id]);
      if (student.length > 0) {
        const user_id = student[0].user_id;
        await connection.execute('DELETE FROM students WHERE id = ?', [id]);
        await connection.execute('DELETE FROM users WHERE id = ?', [user_id]);
        return true;
      }
      return false;
    } finally {
      connection.release();
    }
  }
}

module.exports = Student;
