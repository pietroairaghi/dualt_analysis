-- N Activities per user
CREATE OR REPLACE VIEW n_activities AS (
	SELECT us_user, COUNT(ac_activity) as n_activities FROM users
		NATURAL JOIN activities_users 
		NATURAL JOIN activities_versions
		WHERE acv_type = 'final'
		GROUP BY us_user
);
	
-- N recipes per user
CREATE OR REPLACE VIEW n_recipes AS (
	SELECT us_user, COUNT(ac_activity) as n_recipes FROM users
		NATURAL JOIN activities_users
		NATURAL JOIN activities
	WHERE at_activityType = 'recipe'
		GROUP BY us_user
);

-- N EXPERIENCES per user
CREATE OR REPLACE VIEW n_experiences AS (
	SELECT us_user, COUNT(ac_activity) as n_experiences FROM users
		NATURAL JOIN activities_users
		NATURAL JOIN activities
	WHERE at_activityType = 'experience'
		GROUP BY us_user
);

-- N reflections (grouped by activity) per user
CREATE OR REPLACE VIEW n_reflections AS (
	SELECT us_user, COUNT(DISTINCT ac_activity) as n_reflections FROM reflections 
		GROUP BY us_user
);

-- N reflections (grouped by RECIPE) per user
CREATE OR REPLACE VIEW n_recipe_reflections AS (
	SELECT us_user, COUNT(DISTINCT ac_activity) as n_recipe_reflections FROM reflections 
		NATURAL JOIN activities
	WHERE at_activityType = 'recipe'
		GROUP BY us_user
);

-- N reflections (grouped by RECIPE) per user
CREATE OR REPLACE VIEW n_experience_reflections AS (
	SELECT us_user, COUNT(DISTINCT ac_activity) as n_experience_reflections FROM reflections 
		NATURAL JOIN activities
	WHERE at_activityType = 'experience'
		GROUP BY us_user
);

-- N activities in CURRICULUM per user
CREATE OR REPLACE VIEW n_in_curriculum AS (
	SELECT us_user, COUNT(DISTINCT ac_activity) as n_in_curriculum FROM activities_classification 
		NATURAL JOIN activities_users
	WHERE atcl_classificationType = 'inCurriculum'
		GROUP BY us_user
);

-- N RECIPES in CURRICULUM per user
CREATE OR REPLACE VIEW n_recipes_in_curriculum AS (
	SELECT us_user, COUNT(DISTINCT ac_activity) as n_recipes_in_curriculum FROM activities_classification 
		NATURAL JOIN activities_users
		NATURAL JOIN activities
	WHERE atcl_classificationType = 'inCurriculum' AND at_activityType = 'recipe'
		GROUP BY us_user
);

-- N EXPERIENCES in CURRICULUM per user
CREATE OR REPLACE VIEW n_experiences_in_curriculum AS (
	SELECT us_user, COUNT(DISTINCT ac_activity) as n_experiences_in_curriculum FROM activities_classification 
		NATURAL JOIN activities_users
		NATURAL JOIN activities
	WHERE atcl_classificationType = 'inCurriculum' AND at_activityType = 'experience'
		GROUP BY us_user
);

-- N activities in CURRICULUM per user per semester
CREATE OR REPLACE VIEW n_in_curriculum_semester AS (
	SELECT us_user, 
		COUNT(DISTINCT(CASE WHEN atcl_option = 1 THEN ac_activity END)) as n_in_curriculum_semester1,
		COUNT(DISTINCT(CASE WHEN atcl_option = 2 THEN ac_activity END)) as n_in_curriculum_semester2,
		COUNT(DISTINCT(CASE WHEN atcl_option = 3 THEN ac_activity END)) as n_in_curriculum_semester3,
		COUNT(DISTINCT(CASE WHEN atcl_option = 4 THEN ac_activity END)) as n_in_curriculum_semester4,
		COUNT(DISTINCT(CASE WHEN atcl_option = 5 THEN ac_activity END)) as n_in_curriculum_semester5 
    FROM activities_classification 
		NATURAL JOIN activities_users
	WHERE atcl_classificationType = 'inCurriculum'
		GROUP BY us_user
);

-- N sent feedback per user
CREATE OR REPLACE VIEW n_sent_feedback AS (
	SELECT no_sender as us_user, 
		COUNT(CASE WHEN no_type = 'feedback request' THEN 1 END) AS n_feedback_requests, 
		COUNT(CASE WHEN no_type = 'feedback response' THEN 1 END) AS n_feedback_responses 
	FROM notifications 
	WHERE no_sender IS NOT NULL
	GROUP BY no_sender
);

-- N received feedback per user
CREATE OR REPLACE VIEW n_received_feedback AS (
	SELECT no_recipient as us_user, 
		COUNT(CASE WHEN no_type = 'feedback request' THEN 1 END) AS n_received_feedback_requests, 
		COUNT(CASE WHEN no_type = 'feedback response' THEN 1 END) AS n_received_feedback_responses 
	FROM notifications 
	WHERE no_recipient IS NOT NULL
	GROUP BY no_recipient
);


/* 
	SELECT USERS, USER TYPE (CSV), CLASSESS (CSV), COMPANIES (CSV), 
		N_ACTIVITIES, N_RECIPES, N_EXPERIENCES, N_REFLECTIONS (ALL, RECIPES, EXPERIENCES), N_CURRICULUM.*
*/
SELECT users.us_user as user_id,us_archived as archived,user_type,classes, companies 
	n_activities, n_recipes, n_experiences, n_reflections, n_recipe_reflections, n_experience_reflections,
	n_in_curriculum,n_recipes_in_curriculum,n_experiences_in_curriculum, 
	n_in_curriculum_semester1,n_in_curriculum_semester2,
	n_in_curriculum_semester3,n_in_curriculum_semester4,n_in_curriculum_semester5,
	n_feedback_requests,n_received_feedback_responses,n_received_feedback_requests,n_feedback_responses,
	avg_activity_evaluations, avg_reflection_length, avg_specific_evaluations
	n_files,n_folders
FROM `users` 

	NATURAL JOIN (
		SELECT us_user, GROUP_CONCAT(ut_user_type) as user_type FROM users_userTypes GROUP BY us_user
	) AS T_user_types
	
	NATURAL LEFT JOIN (
		SELECT us_user, GROUP_CONCAT(cl_class) as classes FROM `users_classes` GROUP BY us_user
	) AS T_classes
	
	NATURAL LEFT JOIN (
		SELECT us_user, GROUP_CONCAT(DISTINCT co_company) as companies FROM bosses GROUP BY us_user
	) AS T_companies
	
	NATURAL LEFT JOIN (
		SELECT us_user, COUNT(*) as total_logins FROM LOG_logins GROUP BY us_user
	) AS T_logins
	
	NATURAL LEFT JOIN(
		SELECT us_user, ROUND(AVG(LENGTH(ref_reflection))) AS avg_reflection_length, AVG(ref_evaluation) AS avg_specific_evaluations FROM reflections GROUP BY us_user
	) AS T_reflection_details
	
	NATURAL LEFT JOIN(
		SELECT us_user, AVG(au_evaluation) AS avg_activity_evaluations FROM activities_users GROUP BY us_user
	) AS T_avg_activity_evals
	
	NATURAL LEFT JOIN n_activities
	NATURAL LEFT JOIN n_recipes
	NATURAL LEFT JOIN n_experiences
	NATURAL LEFT JOIN n_reflections
	NATURAL LEFT JOIN n_recipe_reflections
	NATURAL LEFT JOIN n_experience_reflections
	NATURAL LEFT JOIN n_in_curriculum
	NATURAL LEFT JOIN n_recipes_in_curriculum
	NATURAL LEFT JOIN n_experiences_in_curriculum
	NATURAL LEFT JOIN n_in_curriculum_semester
	NATURAL LEFT JOIN n_sent_feedback
	NATURAL LEFT JOIN n_received_feedback
	
	NATURAL LEFT JOIN (
		SELECT us_user, COUNT(us_user) AS n_files FROM files GROUP BY us_user
	) as T_files
	
	NATURAL LEFT JOIN (
		SELECT us_user, COUNT(us_user) AS n_folders FROM packs_users GROUP BY us_user
	) as T_folders
	
WHERE users.us_user>20 ;












-- STEPS info BY ACTIVITY
CREATE OR REPLACE VIEW activity_steps_info AS (
	SELECT st_idStep, ac_activity, ROUND(AVG(LENGTH(st_description))) AS avg_step_len, COUNT(*) AS n_steps 
	FROM steps 
		WHERE LENGTH(st_description) > 0 GROUP BY ac_activity
);


-- ACTIVITIES INFO
SELECT ac_activity AS activity_id, us_user AS user_id, at_activityType, LENGTH(ac_description) AS len_description, 
		ac_atSchool, ac_atInteraziendale, (1-ac_atSchool-ac_atInteraziendale) AS ac_atCompany, avg_step_len, n_steps,
		au_evaluation AS user_evaluation, start_date, ac_date AS end_date, (start_date-ac_date) AS edit_period,
		avg_specific_evaluations, avg_reflection_length, in_curriculum
FROM `activities` NATURAL JOIN activities_versions NATURAL JOIN activities_users

	NATURAL LEFT JOIN (
		SELECT ac_activity_ref AS ac_activity, MIN(ac_date) AS start_date FROM activities_versions NATURAL JOIN activities WHERE acv_type = 'autosave' GROUP BY ac_activity_ref
	) AS T_start_date
	
	NATURAL LEFT JOIN (
		SELECT ac_activity, us_user, ROUND(AVG(LENGTH(ref_reflection))) AS avg_reflection_length, AVG(ref_evaluation) AS avg_specific_evaluations FROM reflections GROUP BY ac_activity,us_user
	) AS T_n_reflections
	
	NATURAL LEFT JOIN(
		SELECT ac_activity, COUNT(fi_file) AS n_images FROM files_steps NATURAL JOIN steps GROUP BY ac_activity
	) AS T_n_files
	
	NATURAL LEFT JOIN(
		SELECT ac_activity, atcl_classificationType, 1 AS in_curriculum FROM activities NATURAL LEFT JOIN activities_classification
			WHERE atcl_classificationType = 'inCurriculum'
	) AS T_check_curriculum

	NATURAL LEFT JOIN activity_steps_info
	
WHERE acv_type = 'final';









