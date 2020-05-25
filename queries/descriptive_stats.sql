-- STUDENTS:

DROP TABLE IF EXISTS all_months_and_years;
CREATE TABLE all_months_and_years (
  month INT NOT NULL,
  activity_school_year INT NOT NULL
);

INSERT INTO all_months_and_years 
    (month, activity_school_year) 
VALUES 
    (1,1),
    (2,1),
    (3,1),
    (4,1),
    (5,1),
    (6,1),
    (7,1),
    (8,1),
    (9,1),
    (10,1),
    (11,1),
    (12,1),
    (1,2),
    (2,2),
    (3,2),
    (4,2),
    (5,2),
    (6,2),
    (7,2),
    (8,2),
    (9,2),
    (10,2),
    (11,2),
    (12,2),
    (1,3),
    (2,3),
    (3,3),
    (4,3),
    (5,3),
    (6,3),
    (7,3),
    (8,3),
    (9,3),
    (10,3),
    (11,3),
    (12,3);
	

CREATE OR REPLACE VIEW users_start_semester AS (
	SELECT us_user, 
		(CASE 
			WHEN MONTH(MIN(date)) < '9' THEN STR_TO_DATE(CONCAT(YEAR(MIN(date))-1,'-09-01'), '%Y-%m-%d') 
			ELSE STR_TO_DATE(CONCAT(YEAR(MIN(date)),'-09-01'), '%Y-%m-%d')
		END) AS start_semester,
		(CASE 
			WHEN MONTH(MIN(date)) < '9' THEN YEAR(MIN(date))-1
			ELSE YEAR(MIN(date))
		END) AS start_year
	FROM log_logins NATURAL JOIN users GROUP BY us_user
);


CREATE OR REPLACE VIEW activities_users_school_year AS (
	SELECT *, ABS(TIMESTAMPDIFF(year,ac_date, start_semester))+1 AS activity_school_year FROM `activities` 
		NATURAL JOIN activities_users 
		NATURAL JOIN users_start_semester
);



CREATE OR REPLACE VIEW logins_users_school_year AS (
	SELECT *, ABS(TIMESTAMPDIFF(year,date, start_semester))+1 AS user_school_year ,
			MONTH(date) as month, DAYOFWEEK(date) AS dayofweek,
			LPAD(HOUR(date),2,'0') AS hour, LPAD(MINUTE(date),2,'0') AS minute
		FROM `LOG_logins` 
			NATURAL JOIN users_start_semester
);

-- number of logins per month
CREATE OR REPLACE VIEW logins_per_month AS (
	SELECT month, user_school_year AS activity_school_year, COUNT(*) AS n_logins 
			FROM logins_users_school_year
				NATURAL JOIN users
				NATURAL JOIN users_usertypes
			
			WHERE ut_user_type = 'studente'
			
		GROUP BY month, user_school_year 
		#GROUP BY start_year, month, user_school_year 
);

-- number of activities per month
CREATE OR REPLACE VIEW activities_per_month_per_user AS (
	SELECT MONTH(ac_date) AS month, activity_school_year, start_year, us_user, COUNT(*) AS n_user_activities, 
			SUM(at_activityType = 'recipe') AS n_user_recipes, 
			SUM(at_activityType = 'experience') AS n_user_experiences 
		FROM activities_users_school_year
		GROUP BY us_user, MONTH(ac_date), activity_school_year
);

-- number of edits per month
CREATE OR REPLACE VIEW activitiy_edits_per_month AS (
	SELECT MONTH(data) AS month, activity_school_year, start_year, COUNT(*) AS n_edits 
	
		FROM LOG_activities 
			NATURAL JOIN activities_users_school_year
		WHERE operazione = 'U' 
		GROUP BY MONTH(ac_date), activity_school_year
		#GROUP BY start_year, MONTH(ac_date), activity_school_year
);


-- number of total files
SELECT MONTH(ac_date) AS month, activity_school_year, COUNT(*) AS n_files FROM files_steps NATURAL JOIN activities_users_school_year NATURAL JOIN steps GROUP BY MONTH(ac_date), activity_school_year;


-- + recipes and experiences
DROP TABLE IF EXISTS V_months_activities_files;
CREATE TABLE V_months_activities_files AS (
	SELECT MONTH(ac_date) AS month, activity_school_year, start_year, COUNT(*) AS n_files, 
		SUM(at_activityType = 'recipe') AS n_files_recipes, 
		SUM(at_activityType = 'experience') AS n_files_experiences,
		ROUND(AVG(n_images)) AS avg_n_files,
		ROUND(AVG(n_images_recipes)) AS avg_n_files_recipes,
		ROUND(AVG(n_images_experiences)) AS avg_n_files_experiences,
		STD(n_images) AS std_n_files,
		STD(n_images_recipes) AS std_n_files_recipes,
		STD(n_images_experiences) AS std_n_files_experiences
		
		FROM activities_users_school_year 
			NATURAL JOIN files_steps
			NATURAL JOIN steps 
			NATURAL JOIN (
				SELECT ac_activity, COUNT(fi_file) AS n_images, 
					SUM(at_activityType = 'recipe') AS n_images_recipes, 
					SUM(at_activityType = 'experience') AS n_images_experiences 
				FROM files_steps NATURAL JOIN steps NATURAL JOIN activities GROUP BY ac_activity) AS T_activities
		GROUP BY MONTH(ac_date), activity_school_year
		#GROUP BY start_year, MONTH(ac_date), activity_school_year
);





-- recipes with feedbacks 
DROP TABLE IF EXISTS V_months_feedbacks;
CREATE TABLE V_months_feedbacks AS (
	SELECT MONTH(ac_date) AS month, activity_school_year, start_year, 
		SUM(has_feedback_request = 1) as n_feedback_requests, SUM(has_feedback_response = 1) as n_feedback_responses,
		SUM(at_activityType = 'recipe' AND has_feedback_request  = 1) AS n_feedback_requests_recipes, 
		SUM(at_activityType = 'recipe' AND has_feedback_response = 1) AS n_feedback_responses_recipes, 
		SUM(at_activityType = 'experience' AND has_feedback_request  = 1) AS n_feedback_requests_experiences, 
		SUM(at_activityType = 'experience' AND has_feedback_response = 1) AS n_feedback_responses_experiences 
		
		FROM activities_users_school_year
			NATURAL JOIN (
				SELECT no_idObject as ac_activity, no_timestamp, 1 AS has_feedback_request
					FROM notifications 
						WHERE no_type = 'feedback request'
					GROUP BY no_idObject
			) AS T_request
			NATURAL LEFT JOIN (
				SELECT no_idObject as ac_activity, no_timestamp, 1 AS has_feedback_response
					FROM notifications 
						WHERE no_type = 'feedback response'
					GROUP BY no_idObject
			) AS T_respose
		GROUP BY MONTH(ac_date), activity_school_year
		#GROUP BY start_year, MONTH(ac_date), activity_school_year
);

-- Total length of recipes description: average + SD / per month
DROP TABLE IF EXISTS len_info_per_months;
CREATE TABLE len_info_per_months AS (
	SELECT MONTH(ac_date) AS month, activity_school_year, start_year, 
		ROUND(AVG(activity_total_length)) AS avg_activity_total_length,
		ROUND(STD(activity_total_length),2) AS std_activity_total_length,
		ROUND(AVG(len_description)) AS avg_len_descriptions,
		ROUND(STD(len_description),2) AS std_len_descriptions,
		ROUND(AVG(len_steps)) AS avg_len_steps,
		ROUND(STD(len_steps),2) AS std_len_steps,
		ROUND(AVG(len_observations)) AS avg_len_observations,
		ROUND(STD(len_observations),2) AS std_len_observations,
		ROUND(AVG(avg_reflection_length)) AS avg_avg_len_reflections,
		ROUND(STD(avg_reflection_length),2) AS std_avg_len_reflections,
		ROUND(AVG(len_bilancio)) AS avg_len_bilancio,
		ROUND(STD(len_bilancio),2) AS std_len_bilancio,
		ROUND(AVG(len_competenze)) AS avg_len_competenze,
		ROUND(STD(len_competenze),2) AS std_len_competenze,
		ROUND(AVG(len_miglioramenti)) AS avg_len_miglioramenti,
		ROUND(STD(len_miglioramenti),2) AS std_len_miglioramenti,
		ROUND(AVG(len_critici)) AS avg_len_critici,
		ROUND(STD(len_critici),2) AS std_len_critici
		
	FROM activities_users_school_year
		NATURAL LEFT JOIN V_activities_info -- see queries.sql
		NATURAL JOIN users_usertypes
		
		GROUP BY MONTH(ac_date), activity_school_year
		#GROUP BY start_year, MONTH(ac_date), activity_school_year
);

CREATE OR REPLACE VIEW all_months_and_years_per_year AS (
	SELECT month, activity_school_year FROM all_months_and_years LEFT JOIN (SELECT user_school_year, COUNT(*) AS n_users_per_year 
		FROM (
			SELECT * FROM `logins_users_school_year` NATURAL JOIN users_userTypes WHERE ut_user_type = 'studente' GROUP BY us_user, user_school_year
		) AS T_logins
		GROUP BY user_school_year) AS T_logins_per_years
		ON all_months_and_years.activity_school_year = T_logins_per_years.user_school_year
);

DROP TABLE IF EXISTS V_months_info;
CREATE TABLE V_months_info AS (
	SELECT *, 
		ROUND(n_feedback_requests/n_activities*100,2) AS perc_total_feedback_requests,
		ROUND(n_feedback_requests_recipes/n_recipes*100,2) AS perc_total_feedback_requests_recipes, 
		ROUND(n_feedback_requests_experiences/n_experiences*100,2) AS perc_total_feedback_requests_experiences, 
		ROUND(n_feedback_responses/n_activities*100,2) AS perc_feedback_responses,
		ROUND(n_feedback_responses_recipes/n_recipes*100,2) AS perc_feedback_responses_recipes, 
		ROUND(n_feedback_responses_experiences/n_experiences*100,2) AS perc_feedback_responses_experiences,
		ROUND(n_in_curriculum/n_activities*100,2) AS perc_in_curriculum,
		ROUND(n_in_curriculum_recipes/n_activities*100,2) AS perc_recipes_in_curriculum,
		ROUND(n_in_curriculum_experiences/n_activities*100,2) AS perc_experiences_in_curriculum,
		ROUND(n_in_curriculum_insert_date/n_activities*100,2) AS perc_in_curriculum_insert_date,
		ROUND(n_in_curriculum_insert_date_recipes/n_activities*100,2) AS perc_recipes_in_curriculum_insert_date,
		ROUND(n_in_curriculum_insert_date_experiences/n_activities*100,2) AS perc_experiences_in_curriculum_insert_date
		
		FROM all_months_and_years_per_year
			NATURAL LEFT JOIN logins_per_month 
			NATURAL LEFT JOIN activities_per_month 
			NATURAL LEFT JOIN (
				SELECT month, activity_school_year, 
					ROUND(AVG(n_user_activities),2) AS avg_n_user_activities,
					ROUND(AVG(n_user_recipes),2) AS avg_n_user_recipes,
					ROUND(AVG(n_user_experiences),2) AS avg_n_user_experiences
					
				FROM activities_per_month_per_user
				GROUP BY month, activity_school_year
			) AS T_avg_activities_per_user
			NATURAL LEFT JOIN V_months_activities_files 
			NATURAL LEFT JOIN V_months_feedbacks
			NATURAL LEFT JOIN (
				SELECT MONTH(ac_date) AS month, activity_school_year, 
					COUNT(*) AS n_in_curriculum,
					SUM(at_activityType = 'recipe') AS n_in_curriculum_recipes,
					SUM(at_activityType = 'experience') AS n_in_curriculum_experiences
					
					FROM `activities_classification`
						NATURAL JOIN activities_users_school_year 
						
					GROUP BY MONTH(ac_date), activity_school_year
			) AS T_in_curriculum
			
			NATURAL LEFT JOIN (
				SELECT month, activity_school_year, COUNT(*) AS n_in_curriculum_insert_date,
					SUM(at_activityType = 'recipe') AS n_in_curriculum_insert_date_recipes,
					SUM(at_activityType = 'experience') AS n_in_curriculum_insert_date_experiences
					FROM(
						SELECT ac_activity, MONTH(MAX(data)) AS month, activity_school_year, at_activityType
									FROM activities_users_school_year
										NATURAL LEFT JOIN LOG_activities_classification
									WHERE atcl_classificationType = 'inCurriculum' AND operazione = "I"
									GROUP BY ac_activity
					) AS T_real_date
				GROUP BY month, activity_school_year
			) AS T_real_date_curriculum
			
			NATURAL LEFT JOIN len_info_per_months
			NATURAL LEFT JOIN activitiy_edits_per_month
			
		ORDER BY month, activity_school_year
);


-- DELETE UNUSED TEMPORARY TABLES (not possible to use the WITH statement because of the differents mysql versions)
DROP TABLE IF EXISTS V_months_feedbacks;
DROP TABLE IF EXISTS V_months_activities_files;




























-- WIP:

SELECT ac_activity_ref AS ac_activity, AVG(avg_step_len) AS modification_avg_len, STD(avg_step_len) FROM 
(SELECT ac_activity, ROUND(AVG(LENGTH(st_description))) AS avg_step_len, COUNT(*) AS n_steps, ac_activity_ref 
	FROM steps NATURAL JOIN activities_versions 
		WHERE LENGTH(st_description) > 0 GROUP BY ac_activity
) AS T_words_activity
GROUP BY ac_activity_ref


SELECT us_user, MONTH(ac_date) AS month, STD(YEAR(ac_date)) AS year_std, STD(DAY(ac_date)) AS day_std, COUNT(ac_activity) AS n_activities FROM activities_users 
	NATURAL JOIN activities_versions
	NATURAL JOIN activities
	WHERE acv_type = 'final'
	GROUP BY us_user, MONTH(ac_date)