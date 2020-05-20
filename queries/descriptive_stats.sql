-- STUDENTS:

CREATE OR REPLACE VIEW users_start_semester AS (
	SELECT us_user, STR_TO_DATE(CONCAT(YEAR(ac_date),'-06-01'), '%Y-%m-%d') AS start_semester
	FROM activities NATURAL JOIN activities_users GROUP BY us_user
);


CREATE OR REPLACE VIEW activities_users_school_year AS (
	SELECT *, ABS(TIMESTAMPDIFF(year,ac_date, start_semester))+1 AS activity_school_year FROM `activities` 
		NATURAL JOIN activities_users 
		NATURAL JOIN users_start_semester
);


-- number of activities per month
CREATE OR REPLACE VIEW activities_per_month AS (
	SELECT MONTH(ac_date) AS month, activity_school_year, COUNT(*) AS n_activities, 
			SUM(at_activityType = 'recipe') AS n_recipes, 
			SUM(at_activityType = 'experience') AS n_experiences 
		FROM activities_users_school_year
		GROUP BY MONTH(ac_date), activity_school_year
);


-- number of total files
SELECT MONTH(ac_date) AS month, activity_school_year, COUNT(*) AS n_files FROM files_steps NATURAL JOIN activities_users_school_year NATURAL JOIN steps GROUP BY MONTH(ac_date), activity_school_year;


-- + recipes and experiences
DROP TABLE IF EXISTS V_months_activities_files;
CREATE TABLE V_months_activities_files AS (
	SELECT MONTH(ac_date) AS month, activity_school_year, COUNT(*) AS n_files, 
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
);





-- recipes with feedbacks 
DROP TABLE IF EXISTS V_months_feedbacks;
CREATE TABLE V_months_feedbacks AS (
	SELECT MONTH(ac_date) AS month, activity_school_year, 
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
);



SELECT *, 
	ROUND(n_feedback_requests/n_activities*100,2) AS perc_total_feedback_requests,
	ROUND(n_feedback_requests_recipes/n_recipes*100,2) AS perc_total_feedback_requests_recipes, 
	ROUND(n_feedback_requests_experiences/n_experiences*100,2) AS perc_total_feedback_requests_experiences, 
	ROUND(n_feedback_responses/n_activities*100,2) AS perc_feedback_responses,
	ROUND(n_feedback_responses_recipes/n_recipes*100,2) AS perc_feedback_responses_recipes, 
	ROUND(n_feedback_responses_experiences/n_experiences*100,2) AS perc_feedback_responses_experiences,
	ROUND(n_in_curriculum/n_activities*100,2) AS perc_in_curriculum,
	ROUND(n_in_curriculum_recipes/n_recipes*100,2) AS perc_recipes_in_curriculum,
	ROUND(n_in_curriculum_experiences/n_experiences*100,2) AS perc_experiences_in_curriculum,
	ROUND(n_in_curriculum_insert_date/n_activities*100,2) AS perc_in_curriculum_insert_date,
	ROUND(n_in_curriculum_insert_date_recipes/n_recipes*100,2) AS perc_recipes_in_curriculum_insert_date,
	ROUND(n_in_curriculum_insert_date_experiences/n_experiences*100,2) AS perc_experiences_in_curriculum_insert_date
	
	FROM activities_per_month 
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
									NATURAL LEFT JOIN log_activities_classification
								WHERE atcl_classificationType = 'inCurriculum' AND operazione = "I"
								GROUP BY ac_activity
				) AS T_real_date
			GROUP BY month, activity_school_year
		) AS T_real_date_curriculum



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