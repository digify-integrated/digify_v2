/* Employee Table */

DROP TABLE IF EXISTS employee;
CREATE TABLE employee (
    employee_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employee_image VARCHAR(500),
    full_name VARCHAR(1000) NOT NULL,
    first_name VARCHAR(300) NOT NULL,
	middle_name VARCHAR(300),
	last_name VARCHAR(300) NOT NULL,
	suffix VARCHAR(10),
	nickname VARCHAR(100),
    private_address VARCHAR(500),
    private_address_city_id INT UNSIGNED,
	private_address_city_name VARCHAR(100),
	private_address_state_id INT UNSIGNED,
	private_address_state_name VARCHAR(100),
	private_address_country_id INT UNSIGNED,
	private_address_country_name VARCHAR(100),
    private_phone VARCHAR(20),
	private_telephone VARCHAR(20),
	private_email VARCHAR(255),
    civil_status_id INT UNSIGNED,
    civil_status_name VARCHAR(100),
    dependents INT DEFAULT 0,
    nationality_id INT UNSIGNED,
    nationality_name VARCHAR(100),
    gender_id INT UNSIGNED,
    gender_name VARCHAR(100),
    religion_id INT UNSIGNED,
    religion_name VARCHAR(100),
    blood_type_id INT UNSIGNED,
    blood_type_name VARCHAR(100),
    birthday DATE,
    place_of_birth VARCHAR(1000),
    home_work_distance DOUBLE DEFAULT 0,
    height FLOAT,
    weight FLOAT,
    employment_status VARCHAR(50) DEFAULT 'Active',
    company_id INT UNSIGNED,
    company_name VARCHAR(100),
    department_id INT UNSIGNED,
    department_name VARCHAR(100),
    job_position_id INT UNSIGNED,
    job_position_name VARCHAR(100),
    work_phone VARCHAR(20),
	work_telephone VARCHAR(20),
	work_email VARCHAR(255),
    manager_id INT UNSIGNED,
    manager_name VARCHAR(1000),
    work_location_id INT UNSIGNED,
    work_location_name VARCHAR(100),
    employment_type_id INT UNSIGNED,
    employment_type_name VARCHAR(100),
    pin_code VARCHAR(100),
    badge_id VARCHAR(100),
    on_board_date DATE,
    off_board_date DATE,
    time_off_approver_id INT UNSIGNED,
    time_off_approver_name VARCHAR(300),
    departure_reason_id INT UNSIGNED,
    departure_reason_name VARCHAR(100),
    detailed_departure_reason VARCHAR(5000),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employee_index_employee_id ON employee(employee_id);
CREATE INDEX employee_index_department_id ON employee(department_id);
CREATE INDEX employee_index_job_position_id ON employee(job_position_id);
CREATE INDEX employee_index_work_location_id ON employee(work_location_id);
CREATE INDEX employee_index_employment_type_id ON employee(employment_type_id);
CREATE INDEX employee_index_private_address_city_id ON employee(private_address_city_id);
CREATE INDEX employee_index_private_address_state_id ON employee(private_address_state_id);
CREATE INDEX employee_index_private_address_country_id ON employee(private_address_country_id);
CREATE INDEX employee_index_civil_status_id ON employee(civil_status_id);
CREATE INDEX employee_index_nationality_id ON employee(nationality_id);
CREATE INDEX employee_index_badge_id ON employee(badge_id);
CREATE INDEX employee_index_employment_status ON employee(employment_status);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Employee Experience Table */

DROP TABLE IF EXISTS employee_experience;
CREATE TABLE employee_experience (
    employee_experience_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    employment_type_id INT UNSIGNED,
    employment_type_name VARCHAR(100),
    company_name VARCHAR(200) NOT NULL,
    location VARCHAR(200),
    work_location_type_id INT UNSIGNED,
    work_location_type_name VARCHAR(100),
    start_month VARCHAR(20),
    start_year VARCHAR(20),
    end_month VARCHAR(20),
    end_year VARCHAR(20),
    job_description VARCHAR(5000),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employee_experience_index_employee_experience_id ON employee_experience(employee_experience_id);
CREATE INDEX employee_experience_index_employee_id ON employee_experience(employee_id);
CREATE INDEX employee_experience_index_employment_type_id ON employee_experience(employment_type_id);
CREATE INDEX employee_experience_index_work_location_type_id ON employee_experience(work_location_type_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Employee Education Table */

DROP TABLE IF EXISTS employee_education;
CREATE TABLE employee_education (
    employee_education_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    school VARCHAR(100) NOT NULL,
    degree VARCHAR(100),
    field_of_study VARCHAR(100),
    start_month VARCHAR(20),
    start_year VARCHAR(20),
    end_month VARCHAR(20),
    end_year VARCHAR(20),
    activities_societies VARCHAR(5000),
    education_description VARCHAR(5000),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employee_education_index_employee_education_id ON employee_education(employee_education_id);
CREATE INDEX employee_education_index_employee_id ON employee_education(employee_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Employee License Table */

DROP TABLE IF EXISTS employee_license;
CREATE TABLE employee_license (
    employee_license_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    licensed_profession VARCHAR(200) NOT NULL,
    licensing_body VARCHAR(200) NOT NULL,
    license_number VARCHAR(200) NOT NULL,
    issue_date DATE NOT NULL,
    expiration_date DATE,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employee_license_index_employee_license_id ON employee_license(employee_license_id);
CREATE INDEX employee_license_index_employee_id ON employee_license(employee_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Employee Emergency Contact Table */

DROP TABLE IF EXISTS employee_emergency_contact;
CREATE TABLE employee_emergency_contact (
    employee_emergency_contact_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    emergency_contact_name VARCHAR(500) NOT NULL,
    relationship_id INT UNSIGNED NOT NULL,
    relationship_name VARCHAR(100) NOT NULL,
    telephone VARCHAR(50),
    mobile VARCHAR(50),
    email VARCHAR(200),
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (relationship_id) REFERENCES relationship(relationship_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employee_emergency_contact_index_employee_emergency_contact_id ON employee_emergency_contact(employee_emergency_contact_id);
CREATE INDEX employee_emergency_contact_index_employee_id ON employee_emergency_contact(employee_id);
CREATE INDEX employee_emergency_contact_index_relationship_id ON employee_emergency_contact(relationship_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */

/* Employee Language Table */

DROP TABLE IF EXISTS employee_language;
CREATE TABLE employee_language (
    employee_language_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY NOT NULL,
    employee_id INT UNSIGNED NOT NULL,
    language_id INT UNSIGNED NOT NULL,
    language_name VARCHAR(100) NOT NULL,
    language_proficiency_id INT UNSIGNED NOT NULL,
    language_proficiency_name VARCHAR(100) NOT NULL,
    created_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_log_by INT UNSIGNED DEFAULT 1,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id),
    FOREIGN KEY (language_id) REFERENCES language(language_id),
    FOREIGN KEY (language_proficiency_id) REFERENCES language_proficiency(language_proficiency_id),
    FOREIGN KEY (last_log_by) REFERENCES user_account(user_account_id)
);

CREATE INDEX employee_language_index_employee_language_id ON employee_language(employee_language_id);
CREATE INDEX employee_language_index_employee_id ON employee_language(employee_id);
CREATE INDEX employee_language_index_language_proficiency_id ON employee_language(language_proficiency_id);

/* ----------------------------------------------------------------------------------------------------------------------------- */