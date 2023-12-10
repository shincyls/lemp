CREATE TABLE admin_info (
	id int(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE DEFAULT UUID(),
	username VARCHAR(32) NOT NULL,
	password VARCHAR(255) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    phone VARCHAR(16) NULL DEFAULT "",
    email VARCHAR(128) NULL DEFAULT "",
    company VARCHAR(128) NULL DEFAULT "",
    company_id int(8) NULL DEFAULT 0,
    adminrole BOOLEAN NOT NULL DEFAULT FALSE,
    adminsion TEXT NULL DEFAULT "",
    suspended int(2) NOT NULL DEFAULT 0,
    signed_on DATETIME NULL DEFAULT NULL,
	created_on DATETIME DEFAULT NOW(),
	modified_on DATETIME ON UPDATE NOW()
);

CREATE TABLE appuser_info (
	id int(10) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    uuid VARCHAR(36) UNIQUE DEFAULT UUID(),
	username VARCHAR(64) NOT NULL,
	password VARCHAR(128) NOT NULL,
    fullname VARCHAR(128) NOT NULL,
    phone VARCHAR(16) NOT NULL DEFAULT "",
    email VARCHAR(128) NOT NULL DEFAULT "",
    balcredit DOUBLE(8,2) DEFAULT 0.0,
    usr_credited DOUBLE(8,2) DEFAULT 0.0,
    usr_debited DOUBLE(8,2) DEFAULT 0.0,
    usr_group INT(2) NOT NULL DEFAULT 0,
    usr_lock INT(2) NOT NULL DEFAULT 0,
    signed_in DATETIME NULL DEFAULT NULL,
    signed_from VARCHAR(16) DEFAULT "mobile",
    signed_device VARCHAR(32) DEFAULT "Nova 5T",
    loc_id int(16) NULL DEFAULT 0,
	created_on DATETIME DEFAULT NOW(),
	modified_on DATETIME ON UPDATE NOW()
);

CREATE TABLE appuser_location (
	id int(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
	user_id int(10) NOT NULL,
    location VARCHAR(64) DEFAULT "ANDES CONDO VILLA",
    address VARCHAR(255) DEFAULT "",
    poscode int(6) NULL,
	city VARCHAR(64) DEFAULT "",
    state VARCHAR(32) DEFAULT "",
    country VARCHAR(64) DEFAULT "MALAYSIA",
    latitude DOUBLE(12,9) NULL,
    longitude DOUBLE(12,9) NULL,
    property_id int(16) NULL,
	created_on DATETIME DEFAULT NOW(),
	modified_on DATETIME ON UPDATE NOW()
);

CREATE TABLE appuser_topup (
	id int(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    unix int(10) NOT NULL DEFAULT UNIX_TIMESTAMP(),
    user_id int(10) NOT NULL,
    username VARCHAR(36) NULL,
    t7to_uid int(10) NULL,
    pay_note VARCHAR(64) DEFAULT "",
    pay_dumb TEXT DEFAULT "",
    pay_type VARCHAR(64) DEFAULT "User Topup Credits",
    pay_amount DOUBLE(8,2) DEFAULT 0.00,
    pay_gateway VARCHAR(64) DEFAULT "FPX",
    pay_gateway_cost DOUBLE(8,2) DEFAULT 0.00,
    pay_status VARCHAR(16) DEFAULT "PENDING",
    rcv_dump TEXT DEFAULT "",
    rcv_status VARCHAR(16) DEFAULT "PENDING",
    rcv_date DATETIME NULL,
    bal_before DOUBLE(8,2) DEFAULT 0.00,
    bal_now  DOUBLE(8,2) DEFAULT 0.00,
    promo_code VARCHAR(16) DEFAULT "",
	created_on DATETIME DEFAULT NOW(),
	modified_on DATETIME ON UPDATE NOW(),
    modified_by DATETIME NULL
);

CREATE TABLE appuser_spend (
	id int(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    unix int(10) NOT NULL DEFAULT UNIX_TIMESTAMP(),
    user_id int(10) NOT NULL,
    username VARCHAR(36) NULL,
	t7fr_uid int(10) NULL,
    transfer_from int(16) NULL,
    product_id int(16) NULL,
    product_desc VARCHAR(255) NOT NULL DEFAULT "",
    product_unit int(2) NOT NULL DEFAULT 1,
    product_price DOUBLE(8,2) NOT NULL DEFAULT 0.00,
    product_total DOUBLE(8,2) NOT NULL DEFAULT 0.00,
    status_datetime DATETIME NULL,
    status_id int(2) NOT NULL DEFAULT 0,
    status_name VARCHAR(16) NOT NULL DEFAULT "PENDING",
    status_refund DOUBLE(8,2) NOT NULL DEFAULT 0.0,
	created_on DATETIME DEFAULT NOW(),
	modified_on DATETIME ON UPDATE NOW(),
    modified_by DATETIME NULL
);

CREATE TABLE admin_log (
	id int(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    admin_id VARCHAR(36) not null,
    username VARCHAR(36) not null,
    action VARCHAR(64) null,
    status VARCHAR(18) null,
    message VARCHAR(255) null,
    receive_dump TEXT null,
    response_dump TEXT null,
	created_on DATETIME DEFAULT NOW()
);

-- LATER
CREATE TABLE appuser_product (
	id int(16) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    spend_id VARCHAR(36) UNIQUE DEFAULT UUID(),
	t7fr_uid int(16) NOT NULL,
    transfer_from int(16) NULL,
    product_id int(16) NULL,
    product_desc VARCHAR(255) NOT NULL DEFAULT "",
    unit_purchase int(2) NOT NULL DEFAULT 1,
    unit_price DOUBLE(8,2) NOT NULL DEFAULT 0.00,
    payment_total DOUBLE(8,2) NOT NULL DEFAULT 0.00,
    check_datetime DATETIME NULL,
    status_id int(2) NOT NULL DEFAULT 0,
    status_name VARCHAR(64) NOT NULL DEFAULT "PENDING",
    status_refund DOUBLE(8,2) NOT NULL DEFAULT 0.0,
	created_on DATETIME DEFAULT NOW(),
	modified_on DATETIME ON UPDATE NOW(),
    modified_by DATETIME NULL
);

-- 0 appuser topup & spend migrate with user id
-- 1 profile & reset password page
-- 2 register & forgot password page
-- 3 fixed datatable cannot reinitialize
-- 4 fixed issues session expired and relogin direct url
-- 5 controllers log table
-- 6 auto refresh value every x seconds
7 appuser dedicated page (profile, *add locations, all topups & orders)
8 search scope
9 data strength validation
-- 10 admin permission change
-- core tables
11 product dedicated page (details, categories, tags, sales, linked product, conditions & spends)
12 product = owner_id(appuser_id), owner_id(appuser_id), title, description, img, category, type(0=local,1=nearby,2=partner), halal?, vege?
13 product.collection = owner_id(appuser_id), appuser_sell_id,product_id,lat,lon,range_km,target_unit(deal success),max_unit(not receive new upon hit target),set_prices(1=130,4=120,3=110,4=100,5=80)
14 product.collection.order = appuser_buy_id,product_id,collection_id  (appuser_order/spend table)
15 product.review future?

-- Front End
9 backend apis
1 vue js
2 aws bucket photo

-- Server
1 tinc
2 no ssl / ssl
3 dns domain name 
