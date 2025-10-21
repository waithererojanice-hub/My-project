-- Departments
INSERT INTO departments (department_name)
VALUES ('Computer Science'),
       ('Business Management'),
       ('Education');

-- Courses
INSERT INTO courses (course_code, course_name, department_id)
VALUES ('CS101', 'Diploma in Information Technology', 1),
       ('BM201', 'Diploma in Business Management', 2),
       ('ED301', 'Bachelor of Education', 3);

-- Semesters
INSERT INTO semesters (semester_name, course_id, department_id)
VALUES ('Semester 1', 1, 1),
       ('Semester 2', 2, 2),
       ('Semester 3', 3, 3);

-- Units
INSERT INTO units (unit_code, unit_name, course_id)
VALUES ('CSU101', 'Introduction to Programming', 1),
       ('BMU201', 'Principles of Management', 2),
       ('EDU301', 'Educational Psychology', 3);

-- Lecturers
INSERT INTO lecturers (full_name, email, password, availability, department_id)
VALUES ('John Mwangi', 'john.mwangi@sunrise.ac.ke', 'lecturer123', 'Full-Time', 1),
       ('Lucy Wanjiru', 'lucy.wanjiru@sunrise.ac.ke', 'lecturer123', 'Part-Time', 2),
       ('Mark Otieno', 'mark.otieno@sunrise.ac.ke', 'lecturer123', 'Visiting', 3);

-- Students
INSERT INTO students (full_name, email, password, course_id, semester_id)
VALUES ('Jane Doe', 'jane.doe@student.sunrise.ac.ke', 'student123', 1, 1),
       ('Brian Kim', 'brian.kim@student.sunrise.ac.ke', 'student123', 2, 2),
       ('Sarah Njeri', 'sarah.njeri@student.sunrise.ac.ke', 'student123', 3, 3);

-- Admins
INSERT INTO admins (full_name, email, password, role)
VALUES ('Samuel Mwangi', 'samuel.mwangi@sunrise.ac.ke', 'admin123', 'Timetabler'),
       ('Anne Kariuki', 'anne.kariuki@sunrise.ac.ke', 'admin123', 'HOD'),
       ('Peter Ouma', 'peter.ouma@sunrise.ac.ke', 'admin123', 'Dean');

-- Rooms
INSERT INTO rooms (room_name, capacity, building_name)
VALUES ('Lab 1', 40, 'ICT Block'),
       ('Room 204', 50, 'Business Block'),
       ('Lecture Hall A', 80, 'Education Block');

-- Timetables
INSERT INTO timetables (course_id, unit_id, lecturer_id, room_id, semester_id, day_of_week, start_time, end_time)
VALUES (1, 1, 1, 1, 1, 'Monday', '08:00:00', '10:00:00'),
       (2, 2, 2, 2, 2, 'Tuesday', '10:00:00', '12:00:00'),
       (3, 3, 3, 3, 3, 'Wednesday', '09:00:00', '11:00:00');