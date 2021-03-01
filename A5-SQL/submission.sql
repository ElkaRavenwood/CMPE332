-- 1) Show the names of all instructors in the Comp. Sci. department who earn more than $70,000.

SELECT name FROM instructor WHERE dept_name="Comp. Sci." AND salary > 70000;

-- 2) List each student id along with the course ids that they take each year.

SELECT ID, course_id FROM takes;

-- 3) Which time slots have classes on Mondays?  Show the output as a single column labeled MondaySlots.  Be sure to check that your results are correct!

SELECT time_slot_id AS MondaySlots FROM time_slot WHERE day="M";

-- 4) Make a list of student names and their course ids.  Order the results alphabetically by student name.

SELECT name, course_id FROM student, takes WHERE student.ID=takes.ID ORDER BY name;

-- 5) For all courses with an instructor taught prior to or during 2010, show the course id, the year taught, and the name of the instructor.  (Use the teaches and instructor tables for this one).

SELECT name, course_id, year FROM instructor, teaches WHERE year < 2011 AND teaches.ID=instructor.ID;

-- 6) How many unique courses were offered in 2010?  By "unique", I mean unique course ids.   Use the section table for this query.  Call the resulting column NumCourses2010.

SELECT DISTINCT course_id as NumCourses2010 FROM section WHERE year=2010;

-- 7) Make a list of all names (instructors and students).  Your resulting table should have one column.

SELECT name FROM instructor UNION SELECT name FROM student;

-- 8)  List the course ID, semester, year and title of each course offered by the Finance department.

SELECT s.course_id, semester, year, title FROM section AS s, course AS c WHERE s.course_id=c.course_id AND c.dept_name="Finance";

-- 9) List the names of all students and the ID of their advisor, if they have one.  In mysql, left/right joins implement outer joins that we used in relational algebra.

SELECT name, i_ID FROM student LEFT JOIN advisor on student.ID=advisor.s_id;

-- 10) For each student who has taken a course, list their name, the name of the student's department and the name of the course that they have taken.

SELECT name, course.dept_name, title FROM student, takes, course WHERE student.ID=takes.ID AND takes.course_id=course.course_id;

-- 11) Make a list containing each student's name and the number of unique courses that they have taken in each year.  Your output should have 3 columns, Student Name, Year and Number of Courses.

SELECT student.name as "Student Name", takes.year as "Year", COUNT(*) as "Number of Courses" FROM student, takes WHERE student.id = takes.id GROUP BY student.id, takes.year;

-- 12) Make a list of courses (course ids) that were offered in both Fall 2009 and in Spring 2010.   (This does not mean either/or, but courses that were offered in both semesters).

SELECT course_id FROM teaches WHERE semester="Fall" and year=2009 INTERSECT SELECT course_id FROM teaches WHERE semester="Spring" and year=2010;

-- 13) Show students (just their ID is enough) who have both an A and an C among the courses that they have taken.

SELECT ID FROM takes WHERE grade="A" INTERSECT SELECT ID FROM takes WHERE grade="C";

-- 14) For all courses that start at 8:00AM, change the time to 9:30AM.

UPDATE time_slot SET start_time="9:30" WHERE start_time="8:00";

-- 15) Delete the Comp. Sci. department.

DELETE FROM department WHERE dept_name="Comp. Sci.";